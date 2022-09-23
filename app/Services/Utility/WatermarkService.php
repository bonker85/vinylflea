<?php
namespace App\Services\Utility;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class WatermarkService {

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

    public function createImageAvatar($imagePath, $imageThumbnailPath)
    {

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
