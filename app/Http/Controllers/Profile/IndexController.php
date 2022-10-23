<?php

namespace App\Http\Controllers\Profile;


use App\Http\Requests\Profile\Advert\AddRequest;
use App\Http\Requests\Profile\Advert\EditRequest;
use App\Models\Advert;
use App\Models\AdvertDialog;
use App\Models\AdvertFavorit;
use App\Models\AdvertImage;
use App\Models\BanUserList;
use App\Models\Edition;
use App\Models\Message;
use App\Models\Style;
use App\Models\User;
use App\Services\AdvertService;
use App\Services\Utility\CDNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            } else {
                $select->where('id', '!=', '4235');
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
        $data['url'] = translate_url($data['name']) . '-u' . $data['user_id'];
        $data['description'] = strip_tags(nl2br($data['description']),'<br><b>');
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
                                'path' => str_replace('public/users', '/users', substr($path, strpos($path, 'public/users')))
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

    public function messages($advertDialogId = null)
    {
        $userId = auth()->user()->id;
        $messages = [];
        if ($advertDialogId) {
            $advertDialog = AdvertDialog::find($advertDialogId);
            if ($advertDialog) {
                $messages = $this->service->getAdvertDialogMessages($advertDialog->id);
                $this->service->clearAdvertDialogDontViewMessages($advertDialog);
            }
            if (!$messages) {
                return abort('404');
            }
        } else {
            $advertDialog = AdvertDialog::select()
                ->where(function($q) use ($userId) {
                    $q->where('from_user_id', $userId)->orWhere('to_user_id', $userId);
                })
                ->orderBy('updated_at', 'DESC')->first();
            if ($advertDialog) {
                $messages = $this->service->getAdvertDialogMessages($advertDialog->id);
                $this->service->clearAdvertDialogDontViewMessages($advertDialog);
            }
        }

        $advertLists = [];
        if ($messages) {
            //
            $advertLists =
                AdvertDialog::selectRaw('*, IF (from_user_id=' . $userId .
                    ', count_not_view_user_from, IF (to_user_id='
                    . $userId . ', count_not_view_user_to, 0)) AS count_messages')
                ->where(function($q) use ($userId) {
                   $q->where('from_user_id', $userId)->orWhere('to_user_id', $userId);
                })
                ->orderBy('updated_at', 'DESC')
                ->get();
        }
        return view('profile.messages', compact('messages', 'advertLists', 'advertDialog'));
    }

    public function addMessage(AdvertDialog $advertDialog)
    {
        $userId = auth()->user()->id;
        if ($advertDialog->to_user_id == $userId && $advertDialog->from_user_id == $userId) {
            abort('404');
        }
        $message = request()->get('message');
        if (($advertDialog->to_user_id == auth()->user()->id ||
            $advertDialog->from_user_id == auth()->user()->id) &&
            $message && mb_strlen($message) <= 1000) {
            $datetime = now();
            $data = [
                'advert_dialog_id' => $advertDialog->id,
                'advert_id' => $advertDialog->advert_id,
                'from_id' => auth()->user()->id,
                'to_id' => (auth()->user()->id == $advertDialog->to_user_id) ?
                    $advertDialog->from_user_id : $advertDialog->to_user_id,
                'message' => $message,
                'created_at' => $datetime,
                'updated_at' => $datetime
            ];
            if (Message::insert($data)) {
                if (auth()->user()->id == $advertDialog->to_user_id) {
                    $advertDialog->count_not_view_user_from = ++$advertDialog->count_not_view_user_from;
                } else {
                    $advertDialog->count_not_view_user_to = ++$advertDialog->count_not_view_user_to;
                }
                $advertDialog->updated_at = $datetime;
                $advertDialog->save();
                if (request()->ajax()) {
                    return [
                        'error' => '',
                        'chat' => $this->service->generateChatBlockForAjaxQuery($advertDialog)
                    ];
                }
                return redirect()->route('profile.messages', $advertDialog->id);
            }
        }
        if (request()->ajax()) {
            return [
                'error' => 'Произошла ошибка при отправке сообщения'
            ];
        }
        request()->session()->flash('success', 'Произошла ошибка при отправке сообщения');
        return redirect()->route('profile.messages', $advertDialog->id);
    }

    public function user(User $user)
    {
        if ((int)$user->role_id === User::ROLE_ADMIN) {
            abort('404');
        }
        $advertList = Advert::select()
                        ->where('status', AdvertService::getStatusByName('activated'))
                        ->where('user_id', $user->id)
                        ->orderBy('up_time', 'DESC')->paginate(20);
      /*  foreach ($advertList as $advert) {
            echo $advert->style_id;echo '<br/>';
            $advert->style->name;
        }
        exit();*/
        return view('user.index', compact('user', 'advertList'));
    }
    public function favorit()
    {
        $favoritLists = AdvertFavorit::select()
                ->where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'DESC')->paginate(20);
        return view('profile.favorit', compact('favoritLists'));
    }

    public function favoritDelete(AdvertFavorit $favorit)
    {
        if ($favorit->user_id == auth()->user()->id) {
            $favorit->delete();
        }
        return redirect()->route('profile.favorit');
    }

    public function users(Request $request)
    {
        if (User::isAdmin()) {
            if ($request->post()) {
                switch($request->action) {
                    case 'add_to_ban':
                        BanUserList::firstOrCreate(['user_id' => $request->user_id]);
                        break;
                    case 'remove_ban':
                        $userBan = BanUserList::where('user_id', $request->user_id)->first();
                        if ($userBan) {
                            $userBan->delete();
                        }
                        break;
                    case 'add_dialog':
                        $dialog = AdvertDialog::firstOrcreate([
                            'advert_id' => 4235, // служебный адверт
                            'from_user_id' => 1,
                            'to_user_id' => $request->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'count_not_view_user_from' => 0,
                            'count_not_view_user_to' => 0
                        ]);
                        return redirect()->route('profile.messages', $dialog->id);
                        break;

                }
            }
            $usersList = User::select()->where('id', '!=', auth()->user()->id)->orderBy('email')->paginate(20);
            return view('profile.users', compact('usersList'));
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
            if (!User::isAdmin()) {
                $data['url'] = translate_url($data['name']) . '-u' . $data['user_id'];
            }
            $data['status'] = 2;
            $data['description'] = strip_tags(nl2br($data['description']),'<br><b>');
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
            if ($vinyl && env('CDN_ENABLE')) {
                $cdnService = new CDNService();
            }
            $offset = 0;
            foreach ($vinyl as $url) {
                if (!$url) {
                   $advertImage = AdvertImage::select()->where('advert_id', $advert->id)
                        ->where('path', 'LIKE', '%vinyl' . ($i+$offset) . '%')->first();
                    if ($advertImage) {
                        if (env('CDN_ENABLE')) {
                            $cdnService->deleteObject($advertImage->path);
                        }
                        $advertImage->delete();
                    }
                    $offset++;
                    continue;
                }
                //если уже были загружены изображения раньше и у них сменился рендж (удалили в середине например)
                if (strpos($url, '/users/') !== false) {
                    if (strpos($url, 'vinyl' . $i) === false) {
                        $oldPath = str_replace('/storage', '', parse_url($url, PHP_URL_PATH));
                        $path = preg_replace('#vinyl[1-4]#is', 'vinyl' . $i, $oldPath);
                        $advertImage = AdvertImage::select()->where('advert_id', $advert->id)
                            ->where('path', $oldPath)
                            ->orderBy('created_at', 'DESC')
                            ->first();
                        if ($advertImage) {
                             $cdnPath = $advertImage->path;
                             $advertImage->path = $path;
                             $advertImage->cdn_status=0;
                             $advertImage->cdn_update_time = time();
                             $advertImage->save();
                            //@todo если файла нет на диске, качаем его с cdn и после удаляем с cdn
                            $realOldPath = Storage::disk('public')->getConfig()['root'] . $oldPath;
                            if (env('CDN_ENABLE')
                                && !file_exists($realOldPath)) {
                                $cdnResult = $cdnService
                                    ->downloadFile($cdnPath, $realOldPath);
                            }
                            if (file_exists($realOldPath)) {
                                rename(
                                    Storage::disk('public')->getConfig()['root'] . $oldPath,
                                    Storage::disk('public')->getConfig()['root'] . $path
                                );
                                if (env('CDN_ENABLE')) {
                                    $cdnService->deleteObject($cdnPath);
                                }

                            }

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
                    $path = str_replace('users/' . auth()->user()->id . '/',
                            'users/' . $userId . '/' . $advert->id . '/', $path);
                    $path = preg_replace('#vinyl_original[1-4]#is', 'vinyl' . $i, $path);
                    if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                        if (rename($pathTmp, $path)) {
                            AdvertImage::firstOrCreate(
                                ['path' => $path],
                                [
                                    'advert_id' => $advert->id,
                                    'path' => str_replace('public/users', '/users', substr($path, strpos($path, 'public/users'))),
                                    'cdn_status' => 0,
                                    'cdn_update_time' => time()
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
            $this->syncAdvertImages($advert->id, $advert->user_id);
            return redirect()->route('profile.adverts', ['status' => AdvertService::STATUS[$data['status']]]);
        } else {
            abort('404');
        }
    }

    private function syncAdvertImages($advertId, $userId)
    {
        $imagesInBase = [];
        $imagePath = Storage::disk('public')->getConfig()['root'] . '/users/' . $userId . '/' . $advertId . '/';
        $realImagesOnDisk = scandir($imagePath);
        $advertImages = AdvertImage::select()->where('advert_id', $advertId)->get();
        $cdnFilesName = [];
        if (env('CDN_ENABLE')) {
            $cdnService = new CDNService();
            $cdnResult = $cdnService->getStorageObjects('/users/1/4289/');
            if (!$cdnResult['error']) {
                $cdnObjects = json_decode($cdnResult['body']);
                foreach ($cdnObjects as $sdnObject) {
                    $cdnFilesName[] = $sdnObject->ObjectName;
                }
            }
        }
        // соотвествие картинок в базе и на диске, чистим в базе лишние имаги если есть
        foreach ($advertImages as $image) {
            if (!file_exists(Storage::disk('public')->getConfig()['root'] . $image->path)) {
                if (!empty($cdnFilesName) && $image->cdn_status) {
                    if (in_array(pathinfo($image->path, PATHINFO_BASENAME), $cdnFilesName)) {
                        continue;
                    }
                }
                $image->delete();
                continue;
            }
            $imagesInBase[] = pathinfo($image->path, PATHINFO_BASENAME);
        }
        //чистим с диска имаги которых нет в базе
        foreach ($realImagesOnDisk as $realImage) {
            if ($realImage == '..' || $realImage == '.') continue;
            if (!in_array($realImage, $imagesInBase)) {
                unlink($imagePath . $realImage);
            }
        }
    }

    public function deleteAdvert(Advert $advert)
    {
        if (($advert->user_id == auth()->user()->id &&
                $advert->status != AdvertService::getStatusByName('moderation')) || User::isAdmin()) {
            AdvertFavorit::where('advert_id', $advert->id)->delete();
            $advertImages = AdvertImage::where('advert_id', $advert->id)->get();
            foreach ($advertImages as $aImage) {
                $imgPath = public_path('storage') . $aImage->path;
                if (file_exists($imgPath)) {
                    $dirPath = dirname($imgPath);
                    rrmdir($dirPath);
                }
                $aImage->delete();
            }
            $advertName = $advert->name;
            $advert->delete();
            request()->session()->flash('success', 'Пластинка (' . $advertName . ') удалена');
            return redirect()->route('profile.adverts');
        }
    }
}
