<?php
namespace App\Services\Utility;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class ImageService {

    public function isImage($image)
    {
        $rules = [
            'image' => 'image'
        ];
        $validator = Validator::make(['image' => $image], $rules);
        return !$validator->fails();
    }

    public function isFileMoreSize($fileSize)
    {
        if ($fileSize > env('MAX_FILE_SIZE')) {
            return true;
        }
        return false;
    }

    public function createTmpImageAvatar($image)
    {
        $userId = Auth::user()->id;
        $fileExt = $image->getClientOriginalExtension();
        $imagePath = $image->getRealPath();
        $tmpPath= Storage::disk('public')->getConfig()['root'] . '/tmp/avatar/' . $userId;
        if (is_dir($tmpPath)) {
            rrmdir($tmpPath);
        }
        $tmpFileName = $userId . '.' . $fileExt;
        $newPath = $tmpPath . '/' . $tmpFileName;
        if(make_directory($tmpPath, 0777, true)) {
            $img = Image::make($imagePath);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($newPath);
            return true;
        } else {
            return false;
        }
    }

    public function createTmpImage($image, $name)
    {
        $userId = Auth::user()->id;
        $fileExt = strtolower($image->getClientOriginalExtension());
        $imagePath = $image->getRealPath();
        $tmpPath= Storage::disk('public')->getConfig()['root'] . '/tmp/' . $userId;
        $tmpFileName = $name . '.' . $fileExt;
        $newPath = $tmpPath . '/' . $tmpFileName;
        if(make_directory($tmpPath, 0777, true)) {
            $img = Image::make($imagePath);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($newPath);
            $originalPath = preg_replace('#/vinyl(\d{1})#is', '/vinyl_original$1', $newPath);
            echo $originalPath;exit();
            $img = Image::make($imagePath);
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($originalPath);
            $this->createImageWatermark(
                $originalPath,
                $originalPath,
                public_path('images/watermarks/watermark.png')
            );
            return true;
        } else {
            return false;
        }
    }


    public function createImageWatermark($imagePath, $newImagePath, $watermarkPath)
    {
       // Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();exit();
        /*$img = Image::make(
            file_get_contents("https://dveri-vdk.ru/wp-content/uploads/2020/04/эко-эко-4-белёный-дуб-до-сатинат-1.jpg"));
        */
        /* insert watermark at bottom-right corner with 10px offset */
        $path = pathinfo($newImagePath, PATHINFO_DIRNAME);
        if(make_directory($path, 0777, true)) {
            $img = Image::make($imagePath);
            $widthWatermarkResize = round($img->width()/3);
            $watermarkPath = $this->resizeWatermark($widthWatermarkResize, $watermarkPath);
            $img->insert($watermarkPath, 'bottom-right', 10, 10);
            $img->save($newImagePath);
        } else {
            echo "Error Create Dir";exit();
        }

    }

    public function createImageThumbnail($imagePath, $imageThumbnailPath)
    {
        $img = Image::make($imagePath);
        $path = pathinfo($imageThumbnailPath, PATHINFO_DIRNAME);
        if(make_directory($path, 0777, true)) {
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($imageThumbnailPath);
        } else {
            echo "Error Create Dir";exit();
        }


    }



    public function addDoorImagesByOriginalPath($originalImagePath)
    {
        $mainImage = str_replace("doors/orgl_files/", "doors/main/", $originalImagePath);
        $mainImagePath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() .
            $mainImage;
        $this->createImageWatermark(
            Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() .
            $originalImagePath,
            $mainImagePath,
            public_path('images/watermarks/watermark.png')
        );
        $thumbImagePath = str_replace('doors/main/', 'doors/thumbnails/', $mainImagePath);
        $this->createImageThumbnail($mainImagePath, $thumbImagePath);
        return '/' . $mainImage;
    }
    private function resizeWatermark($width, $path)
    {
        $newPath = str_replace('.png', $width . '.png', $path);
        if (!file_exists($newPath)) {
            $img = Image::make($path);
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($newPath);
        }
        return $newPath;
    }
}
?>
