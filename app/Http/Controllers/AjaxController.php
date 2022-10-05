<?php

namespace App\Http\Controllers;
use App\Models\AdvertImage;
use App\Services\Utility\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                break;
            case 'search':
                return [
                    'items' =>
                        Door::select(
                            DB::raw("CONCAT('/product/', url) AS url"),
                            DB::raw("CONCAT('/storage', main_image) AS image"),
                            'name',
                            'title as description',
                            'price'
                        )->where('name', 'like', "%" . $request->q. "%")
                        ->where('status', 1)->get()
                ];
                break;
            default:
                abort('404');
                break;
        }
    }
}
