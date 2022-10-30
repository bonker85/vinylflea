<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function adverts() {
        return $this->hasMany(Advert::class, 'style_id', 'id')->where('status', 1)->orderBy('up_time', 'DESC');
    }

    public static function getSlugById($id)
    {
        $res = self::find($id);
        if ($res) {
            return $res->slug;
        } else {
            return 'all';
        }
    }
}
