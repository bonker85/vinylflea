<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function images() {
        return $this->hasMany(AdvertImage::class, 'advert_id', 'id')->orderBy('id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function style()
    {
        return $this->hasOne(Style::class, 'id', 'style_id');
    }
    public function getFormatDate()
    {
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->up_time);
        return $carbon->format('H:i') . ', ' . $carbon->diffForHumans();
    }

    public function isUpTime()
    {
        $carbonLastUpTime = Carbon::createFromFormat('Y-m-d H:i:s', $this->up_time);
        return ($carbonLastUpTime->addHours(24) < Carbon::now());
    }
}
