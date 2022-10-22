<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $guarded = false;
    const TYPES = [
        'cdn_error_update_avatar' => 1,
        'cdn_error_update_advert' => 2,
    ];

}
