<?php

namespace App\Http\Controllers;
use App\Models\Advert;
use App\Models\AdvertDialog;
use App\Models\AdvertFavorit;
use App\Models\AdvertImage;
use App\Models\Message;
use App\Models\User;
use App\Services\Utility\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\TextUI\Exception;

class AjaxController extends Controller
{
    public function __construct(Request $request)
    {
       if (!$request->ajax()) {
           abort('404');
       }
    }

    public function index(Request $request, $param)
    {
        switch($param) {
            case 'avatar_file':
                $error = '';
                $imageService = new ImageService();
                if ($imageService->isImage(request()->file('avatar'))) {
                    if ($imageService->isFileMoreSize(request()->file('avatar')->getSize())) {
                        $error = 'Файл не должен превышать ' . format_size(env('MAX_FILE_SIZE'));
                    } else {
                        if ($imageService->createTmpImageAvatar(request()->file('avatar'))) {
                            $userId = auth()->user()->id;
                            $ext = request()->file('avatar')->getClientOriginalExtension();
                            $fileName = $userId . '.' . $ext;
                            return [
                                'error' => $error,
                                'url' => asset('storage/tmp/avatar/' . $userId . '/' . $fileName . '?t=') . time()
                            ];
                        } else {
                            $error = "Произошла ошибка при загрузке файла";
                        }
                    }
                } else {
                    $error = 'Произошла ошибка при загрузке файла';
                }
                if ($error) {
                    return ['error' => $error];
                }
                break;
            case 'vinyl_file':
                try {
                    $error = '';
                    $imageService = new ImageService();
                    $id = request()->get('id');
                    $fileName = '';
                    if (in_array($id, [1, 2, 3, 4])) {
                        $fileName = 'vinyl' . $id;
                    }
                    if ($id &&
                        $imageService->isImage(request()->file($fileName))) {
                        if ($imageService->isFileMoreSize(request()->file($fileName)->getSize())) {
                            $error = 'Файл не должен превышать ' . format_size(env('MAX_FILE_SIZE'));
                        } else {
                            if ($imageService->createTmpImage(request()->file($fileName), $fileName)) {
                                $userId = auth()->user()->id;
                                $ext = strtolower(request()->file($fileName)->getClientOriginalExtension());
                                return [
                                    'error' => $error,
                                    'url' => asset('storage/tmp/' . $userId . '/' . $fileName . '.' . $ext . '?t=') . time()
                                ];
                            } else {
                                $error = "Произошла ошибка при загрузке файла";
                            }
                        }
                    } else {
                        $error = 'Произошла ошибка при загрузке файла';
                    }
                    if ($error) {
                        return ['error' => $error];
                    }
                } catch (Exception $e) {
                    dd('dfsf');
                    return ['error' => $e->getMessage()];
                }

                break;
            case 'vinyl_delete':
                $id = request()->get('id');
                $ext = request()->get('ext');
                $image = request()->get('image');
                if ($id && $ext) {
                    $userId = auth()->user()->id;
                    $file = Storage::disk('public')->getConfig()['root'] . '/tmp/' .
                        $userId . '/vinyl' . $id . '.' . $ext;
                    if (file_exists($file) && !$image) {
                        unlink($file);
                        $originalFile = str_replace('/vinyl', '/vinyl_original', $file);
                        if (file_exists($originalFile)) {
                            unlink($originalFile);
                        }
                    } else if ($image) {
                        return [
                            'error' => '',
                            'url' => asset('/assets/images/avatars/no-avatar.png?t=') . time()
                        ];
                        /*$advertImage = AdvertImage::select()->where('id', $image)->where('path', 'LIKE', '%/users/' .
                            auth()->user()->id . '/_/vinyl' . $id . '.%');
                        if ($advertImage) {
                            $advertImage->delete();
                            return [
                                'error' => '',
                                'url' => asset('/assets/images/avatars/no-avatar.png?t=') . time()
                            ];
                        } else {
                            return ['error' => 'Произошла ошибка при удалении изображения. Файл не найден'];
                        }*/

                    } else {
                        return ['error' => 'Произошла ошибка при удалении изображения. Файл не найден'];
                    }
                    return [
                        'error' => '',
                        'url' => asset('/assets/images/avatars/no-avatar.png?t=') . time()
                    ];
                } else {
                    return ['error' => 'Произошла ошибка при удалении изображения'];
                }
            case 'show_phone':
                    $error = 'error';
                    $paramType = 'advert';
                    if (!auth()->check() && false) {
                        return ['error' => 'Иформация недоступна'];
                    }
                    if (request()->get('advert')) {
                        $id = request()->get('advert');
                    } else if (request()->get('user_id')) {
                        $id = request()->get('user_id');
                        $paramType = 'user';
                    }
                    if (isset($id) && is_numeric($id)) {
                        switch ($paramType) {
                            case 'advert':
                                $advert = Advert::select()->where('status', 1)->where('id', $id)->first();
                                if ($advert) {
                                    if ($advert->user->phone) {
                                        return [
                                            'error' => '',
                                            'phone' => $advert->user->phone
                                        ];
                                    }
                                }
                            break;
                            case 'user':
                                $user = User::find($id);
                                if ($user) {
                                    return [
                                        'error' => '',
                                        'phone' => $user->phone
                                    ];
                                }
                            break;
                        }

                    }
                    return ['error' => $error];
            case 'front-message':
                $error = 'error';
                if (request()->method() == 'POST' && auth()->check()) {
                    $id = request()->get('id');
                    $message = request()->get('message');
                    $userId = auth()->user()->id;
                    if (is_numeric($id) && $message && mb_strlen($message) <= 1000) {
                        $advert = Advert::select()->where('id', $id)->where('status', 1)->first();
                        if ($advert) {
                            if ($advert->user_id == $userId) {
                                $error = 'Произошла системная ошибка. Нельзя отправить сообщение самому себе.';
                                return ['error' => $error];
                            }
                            try {
                                DB::beginTransaction();
                                $datetime = now();
                                $advertDialog = AdvertDialog::select()
                                    ->where('advert_id', $advert->id)
                                    ->where('from_user_id', auth()->user()->id)->first();
                                if ($advertDialog) {
                                    $advertDialog->count_not_view_user_to = ++$advertDialog->count_not_view_user_to;
                                    $advertDialog->updated_at = $datetime;
                                    $advertDialog->save();
                                } else {
                                    $data = [
                                        'advert_id' => $advert->id,
                                        'from_user_id' => $userId,
                                        'to_user_id' => $advert->user_id,
                                        'count_not_view_user_from' => 0,
                                        'count_not_view_user_to' => 1,
                                        'created_at' => $datetime,
                                        'updated_at' => $datetime
                                    ];
                                    $advertDialog = AdvertDialog::firstOrCreate([
                                        'advert_id' => $advert->id,
                                        'from_user_id' => $userId
                                    ], $data);
                                }
                                $data = [
                                    'advert_dialog_id' => $advertDialog->id,
                                    'advert_id' => $advertDialog->advert_id,
                                    'from_id' => $advertDialog->from_user_id,
                                    'to_id' => $advertDialog->to_user_id,
                                    'message' => $message,
                                    'created_at' => $datetime,
                                    'updated_at' => $datetime
                                ];
                                Message::insert($data);
                                DB::commit();
                                return ['error' => '', 'dialog_id' => $advertDialog->id];
                            } catch (\Exception $exception) {
                                DB::rollback();
                                $error = 'Произошла системная ошибка. Попробуйте отправить сообщение позже';
                            }
                        }
                    }
                }
                return ['error' => $error];
            case 'search':
                $q = strtolower(trim($request->q));
                $path = route('vinyls.details', '') . '/';
                $results = Advert::select(
                        'id',
                        'name',
                        DB::raw('CONCAT("Исполнитель: ", IF(author IS NOT NULL, author, "unknown")) as description'),
                        DB::raw("CONCAT('" . $path . "', url) AS url"),
                    )->where(function($query) use ($q) {
                        $query->whereRaw(DB::raw("LOWER(name) LIKE LOWER('%" . $q . "%')"))
                            ->orWhereRaw(DB::raw("LOWER(author) LIKE LOWER('%" . $q . "%')"));
                    })
                    ->where('status', 1)->limit(20)->get();
                $searchRes = [];
                foreach ($results as $key => $result) {
                   $searchRes[$key]['name'] = $result->name;
                   $searchRes[$key]['description'] = $result->description;
                   $searchRes[$key]['url'] = $result->url;
                   $image = AdvertImage::select('path')
                               ->where('advert_id', $result->id)
                               ->orderBy('id')
                               ->first();
                   if ($image) {
                       $searchRes[$key]['image'] = asset('/storage' . $image->path);
                   }
                }
                return [
                    'items' =>
                     $searchRes
                ];
                break;
            case 'favorit':
                $user_id = request()->user_id;
                $advert_id = request()->advert_id;
                if (is_numeric($user_id) && is_numeric($advert_id)
                    && auth()->check() && auth()->user()->id == $user_id) {
                    $advert = Advert::find($advert_id);
                    if ($advert && $advert->user_id != $user_id) {
                        $favorit = AdvertFavorit::select()
                            ->where('advert_id', $advert->id)
                            ->where('user_id', $user_id)->first();
                        if ($favorit) {
                            $favorit->delete();
                            return ['res' => false];
                        } else {
                            $data = ['advert_id' => $advert->id, 'user_id' => $user_id];
                            AdvertFavorit::firstOrCreate($data);
                            return ['res' => true];
                        }
                    }
                }
                return ['res' => false];
                break;
            default:
                abort('404');
                break;
        }
    }
}
