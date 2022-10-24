<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertImage extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function advert() {
        return $this->hasOne(Advert::class, 'id', 'advert_id');
    }
}
