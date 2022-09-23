<?php

namespace App\Http\Controllers;
use App\Services\Utility\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
