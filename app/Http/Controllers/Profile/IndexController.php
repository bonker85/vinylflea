<?php

namespace App\Http\Controllers\Profile;


use App\Http\Requests\Profile\Advert\AddRequest;
use App\Http\Requests\Profile\Advert\EditRequest;
use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\Edition;
use App\Models\Style;
use App\Models\User;
use App\Services\AdvertService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class IndexController extends BaseController
{
    public function index(Request $request, $status = 'activated')
    {
        $statusId = AdvertService::getStatusByName($status);
        if ($statusId) {
            $userId = auth()->user()->id;
            $advert_counts = AdvertService::getCountStatus($userId);
            $select = Advert::select()
                ->where('status', AdvertService::getStatusByName($status))
                ->orderBy('updated_at', 'DESC');
            if (!User::isAdmin()) {
                $select->where('user_id', $userId);
            }
            $advertList = $select->paginate(10);;
            return view('profile.adverts', compact('status', 'advert_counts', 'advertList', ));
        } else {
            return abort(404);
        }

    }

    public function deactivateAdvert($id)
    {
        $advert = Advert::select()->where('id', $id)->where('user_id', auth()->user()->id)->first();
        if ($advert &&
                $advert->status != AdvertService::getStatusByName('moderation') &&
                $advert->status != AdvertService::getStatusByName('rejected')
            ) {
            $advert->status = AdvertService::getStatusByName('deactivated');
            $advert->updated_at = now();
            $advert->save();
            return redirect()->route('profile.adverts', ['status' => 'deactivated']);
        } else {
            abort(404);
        }
    }

    public function activateAdvert($id)
    {
        $advert = Advert::select()->where('id', $id)->where('user_id', auth()->user()->id)->first();
        if ($advert &&
            $advert->status != AdvertService::getStatusByName('moderation') &&
            $advert->status != AdvertService::getStatusByName('rejected')) {
            $advert->status = AdvertService::getStatusByName('activated');
            $advert->updated_at = now();
            $advert->save();
            return redirect()->route('profile.adverts', ['status' => 'activated']);
        } else {
            abort(404);
        }
    }

    public function upAdvert($id)
    {
        $select = Advert::select()->where('id', $id);
        $admin = false;
        if ((int)auth()->user()->role_id === User::ROLE_ADMIN) {
            $admin = true;
        } else {
            $select->where('user_id', auth()->user()->id);
        }
        $advert = $select->first();
        if ($advert && ($advert->isUpTime() || $admin)) {
            $advert->up_time = now();
            $advert->save();
            return redirect()->route('profile.adverts', ['status' => 'activated']);
        } else {
            abort(404);
        }
    }

    public function settings()
    {
        if (request()->post()) {
            switch (request()->get('action')) {
                case 'info':
                    $this->service->editInfo();
                break;
                case 'email':
                    $this->service->editEmail();
                    break;
                case 'password':
                    $this->service->editPassword();
                    break;
            }
        }
        $cities = DB::table('city')->select('name')->orderBy('name')->get();
        $user = auth()->user();
        return view('profile.settings', compact('user', 'cities'));
    }

    public function addAdvert()
    {
        $styles = Style::select()->orderBy('name')->get();
        $editions = Edition::select()->orderBy('name')->get();
        return view('profile.add_advert', compact('styles', 'editions'));
    }

    public function storeAdvert(AddRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['url'] = translate_url($data['name']) . '-' . $data['user_id'];
        $vinyl = $data['vinyl'];
        unset($data['vinyl']);
        $advert = Advert::firstOrCreate(['url' => $data['url']], $data);
        AdvertImage::where('advert_id', $advert->id)->delete();
        $i = 1;
        foreach ($vinyl as $url) {
            if (!$url) {
                continue;
            }
            $pathTmp = Storage::disk('public')->getConfig()['root'] .
                str_replace([env('APP_URL'), 'storage/','vinyl'], ['', '', 'vinyl_original'], parse_url($url)['path']);
            if (file_exists($pathTmp)) {
                $path = str_replace('/tmp/', '/users/', $pathTmp);
                $userId = auth()->user()->id;
                $path = str_replace('users/' . $userId . '/', 'users/' . $userId . '/' . $advert->id . '/' , $path);
                $path = preg_replace('#vinyl_original[1-4]#is', 'vinyl' . $i, $path);
                if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                    if(rename($pathTmp, $path)) {
                        AdvertImage::firstOrCreate(
                            ['path' => $path],
                            [
                                'advert_id' => $advert->id,
                                'path' => str_replace('public', '', substr($path, strpos($path, 'public')))
                            ]);
                    }
                }
            }
            $i++;
        }
        $removePath = Storage::disk('public')->getConfig()['root'] . '/tmp/' . auth()->user()->id;
        if (is_dir($removePath)) {
            rrmdir($removePath);
        }
        return redirect()->route('profile.adverts', ['status' => 'moderation']);
    }

    public function editAdvert(Advert $advert)
    {
        if (($advert->user_id == auth()->user()->id &&
            $advert->status != AdvertService::getStatusByName('moderation')) || User::isAdmin()) {
            $styles = Style::select()->orderBy('name')->get();
            $editions = Edition::select()->orderBy('name')->get();
            return view('profile.edit_advert', compact('styles', 'editions', 'advert'));
        } else {
            abort('404');
        }
    }

    public function updateAdvert(EditRequest $request, Advert $advert)
    {
        if (($advert->user_id == auth()->user()->id &&
                $advert->status != AdvertService::getStatusByName('moderation')) || User::isAdmin()) {
            $data = $request->validated();
            $data['user_id'] = $advert->user_id;
            $data['url'] = translate_url($data['name']) . '-' . $data['user_id'];
            $data['status'] = 2;
            if (User::isAdmin()) {
                $data['status'] = $request->get('status');
                if ($data['status'] == AdvertService::getStatusByName('rejected')) {
                    $data['reject_message'] = $request->get('reject_message');
                }
            }
            $vinyl = $data['vinyl'];
            unset($data['vinyl']);
            if ($advert->status == AdvertService::getStatusByName('moderation') && !$advert->up_time) {
                $advert->up_time = now();
            }
            $advert->update($data);
            $i = 1;
            foreach ($vinyl as $url) {
                if (!$url) {
                    $advertImage = AdvertImage::select()->where('advert_id', $advert->id)
                        ->where('path', 'LIKE', '%vinyl' . $i . '%')->first();
                    if ($advertImage) {
                        $advertImage->delete();
                    }
                    continue;
                }
                if (strpos($url, '/users/') !== false) {
                    if (strpos($url, 'vinyl' . $i) === false) {
                        $oldPath = $url;
                        $path = preg_replace('#vinyl[1-4]#is', 'vinyl' . $i, $url);
                        $advertImage = AdvertImage::select()->where('advert_id', $advert->id)->where('path', $oldPath)->first();
                        if ($advertImage) {
                            $advertImage->path = $path;
                            $advertImage->save();
                            rename(
                                Storage::disk('public')->getConfig()['root'] . $oldPath,
                                Storage::disk('public')->getConfig()['root'] . $path
                            );
                        }
                    }
                    $i++;
                    continue;
                }
                $pathTmp = Storage::disk('public')->getConfig()['root'] .
                    str_replace([env('APP_URL'), 'storage/', 'vinyl'], ['', '', 'vinyl_original'], parse_url($url)['path']);
                if (file_exists($pathTmp)) {
                    $path = str_replace('/tmp/', '/users/', $pathTmp);
                    $userId = $advert->user_id;
                    $path = str_replace('users/' . $userId . '/', 'users/' . $userId . '/' . $advert->id . '/', $path);
                    $path = preg_replace('#vinyl_original[1-4]#is', 'vinyl' . $i, $path);
                    if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                        if (rename($pathTmp, $path)) {
                            AdvertImage::firstOrCreate(
                                ['path' => $path],
                                [
                                    'advert_id' => $advert->id,
                                    'path' => str_replace('public', '', substr($path, strpos($path, 'public')))
                                ]);
                        }
                    }
                }
                $i++;
            }
            $removePath = Storage::disk('public')->getConfig()['root'] . '/tmp/' . $advert->user_id;
            if (is_dir($removePath)) {
                rrmdir($removePath);
            }
            return redirect()->route('profile.adverts', ['status' => AdvertService::STATUS[$data['status']]]);
        } else {
            abort('404');
        }
    }
}
