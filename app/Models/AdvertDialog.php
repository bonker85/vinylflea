<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertDialog extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function advert()
    {
        return $this->hasOne(Advert::class, 'id', 'advert_id');
    }

    public function toUser()
    {
        return $this->hasOne(User::class, 'id', 'to_user_id');
    }

    public function fromUser()
    {
        return $this->hasOne(User::class, 'id', 'from_user_id');
    }
}
