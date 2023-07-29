<?php
namespace App\Services;

use App\Models\Advert;
use App\Models\AdvertFavorit;
use App\Models\AdvertImage;
use App\Models\Log as DbLog;
use App\Models\Style;
use App\Models\User;
use App\Services\Utility\CDNService;
use App\Services\Utility\ImageService;
use Illuminate\Support\Facades\DB;

class AdvertService {

    const STATES = [
        1 => 'Новое',
        2 => 'Б/y',
    ];
    const STATUS = [
        1 => 'activated',
        2 => 'moderation',
        3 => 'rejected',
        4 => 'deactivated'
    ];

    const DEAL = [
        'sale' => 'Продам',
        'exchange' => 'Обменяю',
        'free' => 'Отдам даром'
    ];

    const ADVERT_LIMIT = 10;

    public static function getCountStatus($userId)
    {
        if (!$userId) return false;
        $advert_counts = [];
        foreach (self::STATUS AS $status => $name) {
            $select = Advert::select('id')->where('status', $status);
            if (!User::isAdmin()) {
                $select->where('user_id', $userId);
            } else {
                //убрать служебное объявление
                $select->where('id', '!=', 4235);
            }
            $advert_counts[$name] = $select->count();
        }
        return $advert_counts;
    }

    public static function getStatusByName($status)
    {
        return array_search($status, self::STATUS);
    }

    public static function isUserAdvertsLimit($userId)
    {
        if (User::isMyUsers()) return false;
        return true;
        return Advert::select()->where('user_id', $userId)->count() > self::ADVERT_LIMIT;
    }
    public static function getMainImage($advertId)
    {
        return AdvertImage::select()->where('advert_id', $advertId)->orderBy('id')->limit(1);
    }

    public static function deleteAdvert($advert)
    {
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
        $advert->delete();
    }

    public static function createAdThumb()
    {
        /**
         * Нарезка тумбов на основное изображение
         */
        $imageService = new ImageService();
        $time = time();
        $advertImages = AdvertImage::select()
            ->where('thumb', 0)->where('path','LIKE', '%vinyl1%')->get();
        foreach ($advertImages as $image) {
            $filePath = storage_path('app/public') . $image->path;
            // если файла нет, забираем его с cdn потом удаляем
            $fromCdn = false;
            if (!file_exists($filePath) && env('CDN_ENABLE') && (int)$image->cdn_status) {
                $url = env('CDN_HOST') . $image->path.  '?tm=' . $image->cdn_update_time;
                $content = @file_get_contents($url);
                if ($content) {
                    if (make_directory(dirname($filePath), 0777, true)) {
                        file_put_contents($filePath, $content);
                        $fromCdn = true;
                    }

                }
            }
            if (file_exists($filePath)) {
                $fileThumbPath =
                    storage_path('app/public') .
                    str_replace('/users/',
                        '/advert_thumbs/', $image->path);
                if ($imageService->createImageThumbnail($filePath, $fileThumbPath)) {
                    $image->thumb = 1;
                    $image->thumb_update_time = $time;
                }
                $image->save();
                if ($fromCdn) {
                    //   unlink($filePath);
                    rrmdir(dirname($filePath));
                }
            }
        }
    }

    public static function updateAdvertsOnCDN()
    {
        $cdnService = new CDNService();
        $now = now();
        $time = time();
        $advertImages = AdvertImage::select("id", "path", "cdn_status")
            ->where("cdn_status", 0)
            ->get();
        foreach ($advertImages as $aImage) {
            $path = $aImage->path;
            $filePath = storage_path('app/public') . $path;
            if (file_exists($filePath)) {
                $storagePath =  $path;
                $res = $cdnService->uploadFile($filePath, $storagePath);
                if (!$res["error"]) {
                    $aImage->cdn_status = 1;
                    $aImage->cdn_update_time = $time;
                    $aImage->save();
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                } else {
                    DbLog::insert([
                        'type' => DbLog::TYPES['cdn_error_update_advert'] ,
                        'message' => 'Send Request Error: AdvertImageId' . $aImage->id . ", Body Output:" . $res['body'],
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            } else {
                echo 'error:' . $filePath;exit();
                DbLog::insert([
                    'type' => DbLog::TYPES['cdn_error_update_advert'] ,
                    'message' => 'File Exist Error: AdvertImageId ' . $aImage->id . ', ' .
                        "image don't exist on disc, path: " . $aImage->path,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }

    public static function recountStylesAdverts()
    {
        $stylesCount = Advert::select('style_id', Db::raw("COUNT(*) AS cnt"))
            ->groupBy('style_id')
            ->where('id', '!=', 4235)
            ->where('status', 1)
            ->get();
        $updateStylesIds = [];
        foreach ($stylesCount as $stCount) {
            $style = Style::find($stCount->style_id);
            if ($style) {
                $updateStylesIds[] = $stCount->style_id;
                $style->count = $stCount->cnt;
                $style->save();
            }
        }
        Style::whereNotIn('id', $updateStylesIds)->update(['count' => 0]);
    }

    public static function relationAdverts($advert)
    {
        $limitRelation = 10;
        $allItems = new \Illuminate\Database\Eloquent\Collection;
        $relationAdverts = [];
        //находим другие адверты с этим артистом
        if ($advert->discogs_author_ids) {
            $selectRelationAdverts = Advert::select()
                ->where('status', 1)
                ->where('id', '!=', $advert->id)
                ->where('discogs_author_ids', $advert->discogs_author_ids)
                ->orderBy('up_time', 'DESC');
            //Various
            if ($advert->discogs_author_ids == 194) {
                $selectRelationAdverts = $selectRelationAdverts->where('style_id', $advert->style_id);
            }
            $relationAdverts = $selectRelationAdverts->get();
            if ($relationAdverts) {
                $allItems = $allItems->merge($relationAdverts);
            }
        }
        // если адвертов меньше relationLimit дополняем их адвертами того же стиля
        if (count($relationAdverts) < $limitRelation) {
            $relationAdvertsStyle = Advert::select()
                ->where('status', 1)
                ->where('style_id', $advert->style_id)
                ->inRandomOrder()
                ->limit($limitRelation - count($relationAdverts))
                ->where('id', '!=', $advert->id)
                ->get();
            if ($relationAdvertsStyle) {
                $allItems = $allItems->merge($relationAdvertsStyle);
            }
        }
        return $allItems;
    }
}
?>
