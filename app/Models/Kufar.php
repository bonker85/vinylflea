<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kufar extends Model
{
    use HasFactory;

    protected $table = 'kufar';
    public $timestamps = false;
    protected $guarded = false;

}
