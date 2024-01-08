<?php

namespace App\Http\Controllers;
use App\Models\Advert;
use App\Models\AdvertDialog;
use App\Models\AdvertFavorit;
use App\Models\AdvertImage;
use App\Models\Edition;
use App\Models\Message;
use App\Models\Style;
use App\Models\User;
use App\Services\AdvertService;
use App\Services\Utility\DiscogsService;
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
                    if (!$image) {
                        if (file_exists($file)) {
                            unlink($file);
                        }
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
                    if (!auth()->check()) {
                        return ['mess' => 'Доступно зарегистрированным пользователям'];
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
                                            'mess' => $advert->user->phone
                                        ];
                                    }
                                }
                            break;
                            case 'user':
                                $user = User::find($id);
                                if ($user) {
                                    return [
                                        'mess' => $user->phone
                                    ];
                                }
                            break;
                        }

                    }
                    return ['error' => $error];
            case 'front-message':
                $error = 'error';
                if (request()->method() == 'POST' && auth()->check() && auth()->user()->email_verified_at) {
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
                                Message::firstOrCreate([
                                    'message' => $message,
                                    'advert_id' => $advertDialog->advert_id,
                                    'created_at' => $datetime
                                ], $data);
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
            case 'search_edition':
                $q = str_replace("\"", "'", strtolower(trim($request->q))) . '%';
                $labels = Edition::select()
                    ->where(DB::raw("LCASE(name)"), 'LIKE', DB::raw("LCASE(\"".$q."\")"))->limit(10)->get();
                return [
                    'items' => $labels
                ];
                break;
            case 'search':
                $q = '%' . str_replace("\"", "'", strtolower(trim($request->q))) . '%';
                $path = route('vinyls.details', '') . '/';
                $select = Advert::select(
                        'id',
                        'name',
                        'author',
                        'deal',
                        'price',
                        'user_id',
                        DB::raw("CONCAT('" . $path . "', url) AS url"),
                    )->where(function($query) use ($q) {
                        $query->where(DB::raw("LCASE(name)"), 'LIKE', DB::raw("LCASE(\"".$q."\")"))
                            ->orWhere(DB::raw("LCASE(author)"), 'LIKE', DB::raw("LCASE(\"".$q."\")"));
                    })->limit(5)->orderBy('up_time', 'DESC');
                if ($request->profile) {
                    $statusId = AdvertService::getStatusByName($request->profile);
                    if (is_numeric($statusId)) {
                        $select->where('status', $statusId);
                    }
                } else {
                    $select->where('status', 1);
                }
                if ($request->user_id && is_numeric($request->user_id)) {
                    if (!User::isAdmin()) {
                        $select->where('user_id', $request->user_id);
                    }
                }
                if ($request->style && is_numeric($request->style)) {
                    $select->where('style_id', $request->style);
                }
                $results = $select->get();
                $searchRes = [];
                foreach ($results as $key => $result) {
                   $searchRes[$key]['name'] = $result->name;
                   $searchRes[$key]['id'] = $result->id;
                   if ($result->author) {
                       $searchRes[$key]['description'] = 'Исполнитель: <b>' . $result->author . '</b>';
                   } else {
                       $searchRes[$key]['description'] = 'Исполнитель: Не указан';
                   }
                   if ($result->user_id == 11 || $result->user_id == 6) {
                       $searchRes[$key]['price'] = 'цена договорная';
                   } else {
                       switch ($result->deal) {
                           case "sale":
                               $searchRes[$key]['price'] = str_replace('.00', '', $result->price) . ' р.';
                               break;
                           case "free":
                               $searchRes[$key]['price'] = 'Отдам даром';
                               break;
                           case "exchange":
                               $searchRes[$key]['price'] = 'Обменяю';
                               break;
                       }
                   }
                   if ($request->profile) {
                       if ($request->profile !== AdvertService::STATUS[2] || User::isAdmin()) {
                           $searchRes[$key]['url'] = route('profile.edit_advert', [
                               'advert' => $searchRes[$key]['id']
                           ]);
                       }

                   } else {
                       $searchRes[$key]['url'] = $result->url;
                   }
                   $image = AdvertImage::select()
                               ->where('advert_id', $result->id)
                               ->orderBy('id')
                               ->first();
                   if ($image) {
                       $searchRes[$key]['image'] = thumb_url(asset('/storage' . $image->path), $image);
                   } else {
                       $searchRes[$key]['image'] = asset('/assets/images/release/no-release.png');
                   }
                }
                if ($searchRes) {
                    $key = count($searchRes);
                    if ($request->user_id && is_numeric($request->user_id)) {
                        if ($request->style && is_numeric($request->style)) {
                            $searchRes[$key]['url'] =
                                route('user', [
                                    'user' => $request->user_id,
                                    'style_id' => $request->style
                                ]) . '?uq=' . $request->q;
                        } else if ($request->profile) {
                            $searchRes[$key]['url'] = route('profile.adverts', [
                                    $request->profile
                                ]) . '?uq=' . $request->q;
                        } else {
                            $searchRes[$key]['url'] = route('user', $request->user_id) . '?uq=' . $request->q;
                        }
                    } else {
                        if ($request->style && is_numeric($request->style)) {
                            $searchRes[$key]['url'] = route('vinyls.style', Style::getSlugById($request->style)) . '?q=' . $request->q;
                        } else {
                            $searchRes[$key]['url'] = route('vinyls.styles') . '?q=' . $request->q;
                        }

                    }
                    $searchRes[$key]['description'] = '<div class="button-search">Смотреть все результаты</div>';
                }
                return [
                    'items' =>
                     $searchRes
                ];
                break;
            case 'ad_search':
                if (auth()->check()) {
                    $q = '%' . str_replace("\"", "'", strtolower(trim($request->q))) . '%';
                    $select = Advert::select(
                        'id',
                        'name',
                        'author',
                        'deal',
                        'price',
                        'status'
                    )->where(function($query) use ($q) {
                        $query->where(DB::raw("LCASE(name)"), 'LIKE', DB::raw("LCASE(\"".$q."\")"))
                            ->orWhere(DB::raw("LCASE(author)"), 'LIKE', DB::raw("LCASE(\"".$q."\")"));
                    })->limit(5)->orderBy('up_time', 'DESC');
                    if (!User::isAdmin()) {
                        $select->where('user_id', auth()->user()->id);
                    }
                    $results = $select->get();
                    $searchRes = [];
                    foreach ($results as $key => $result) {
                        $searchRes[$key]['name'] = $result->name;
                        $searchRes[$key]['url'] = route('profile.adverts',
                            [
                                'status' => AdvertService::STATUS[$result->status],
                                'advert' => $result->id
                            ]);
                        if ($result->author) {
                            $searchRes[$key]['description'] = 'Исполнитель: <b>' . $result->author . '</b>';
                        } else {
                            $searchRes[$key]['description'] = 'Исполнитель: Не указан';
                        }
                        switch ($result->deal) {
                            case "sale":
                                $searchRes[$key]['price'] = str_replace('.00', '', $result->price) . ' р.';
                                break;
                            case "free":
                                $searchRes[$key]['price'] = 'Отдам даром';
                                break;
                            case "exchange":
                                $searchRes[$key]['price'] = 'Обменяю';
                                break;
                        }
                        $image = AdvertImage::select()
                            ->where('advert_id', $result->id)
                            ->orderBy('id')
                            ->first();
                        if ($image) {
                            $searchRes[$key]['image'] = thumb_url(asset('/storage' . $image->path), $image);
                        } else {
                            $searchRes[$key]['image'] = asset('/assets/images/release/no-release.png');
                        }
                    }
                    if ($searchRes) {
                        $key = count($searchRes);
                        $searchRes[$key]['url'] = route('vinyls.styles') . '?q=' . $request->q;
                        $searchRes[$key]['description'] = '<div class="button-search">Смотреть все результаты</div>';
                    }
                    return [
                        'items' =>
                            $searchRes
                    ];
                }
                return [];
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
            case 'check_discogs':
                $data['name'] = $request->name;
                $data['author'] = $request->author;
                $data['year'] = $request->year;
                $result = DiscogsService::getReleases($data);
                if (isset($result['error'])) {
                    return $result['error'];
                } else {
                    if (isset($result['error_year'])) {
                        return  "<div style='color: red; font-weight: bold'> По "
                            . $data['year'] . " году релиз не найден</div>
                             <div style='font-weight: bold;margin-bottom: -10px'>Возможные результаты:</div><hr/>";
                    } else if ($result['releases']){
                        return '<hr/>' .
                            view('includes.releases', ['releases' => $result['releases'], 'relation_release' => true]);
                    } else {
                        return  "<div style='color: red; font-weight: bold'>
                                        По запросу исполнитель - " . $data['author'] . ", название - "
                        . $data['name'] . ', год - ' . $data['year'] . " ничего не найдено</div><hr/>";
                    }

                }
                break;
            default:
                abort('404');
                break;
        }
    }
}
