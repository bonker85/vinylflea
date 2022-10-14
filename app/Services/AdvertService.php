<?php
namespace App\Services;

use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\User;

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
    public static function getCountStatus($userId)
    {
        if (!$userId) return false;
        $advert_counts = [];
        foreach (self::STATUS AS $status => $name) {
            $select = Advert::select('id')->where('status', $status);
            if (!User::isAdmin()) {
                $select->where('user_id', $userId);
            }
            $advert_counts[$name] = $select->count();
        }
        return $advert_counts;
    }

    public static function getStatusByName($status)
    {
        return array_search($status, self::STATUS);
    }

    public static function getMainImage($advertId)
    {
        return AdvertImage::select()->where('advert_id', $advertId)->orderBy('id')->limit(1);
    }
}
?>
