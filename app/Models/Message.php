<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function advert()
    {
        return $this->hasOne(Advert::class, 'id', 'advert_id');
    }

    public function toUser()
    {
        return $this->hasOne(User::class, 'id', 'to_id');
    }

    public function fromUser()
    {
        return $this->hasOne(User::class, 'id', 'from_id');
    }

    public function getFormatDate()
    {
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at);
        return $carbon->format('H:i') . ', ' . $carbon->diffForHumans();
    }
}
