<?php
namespace App\Services;

use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\Log as DbLog;
use App\Models\Style;
use App\Models\User;
use App\Services\Utility\CDNService;
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

    const ADVERT_LIMIT = 30;

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
        return Advert::select()->where('user_id', $userId)->count() > self::ADVERT_LIMIT;
    }
    public static function getMainImage($advertId)
    {
        return AdvertImage::select()->where('advert_id', $advertId)->orderBy('id')->limit(1);
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
                    unlink($filePath);
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
        foreach ($stylesCount as $stCount) {
            $style = Style::find($stCount->style_id);
            if ($style) {
                $style->count = $stCount->cnt;
                $style->save();
            }
        }
    }
}
?>
