<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
    use HasFactory;
    public $timestamps = false;

    public static function getIdByName($name)
    {
        $edition = self::select()->where('name', $name)->first();
        if ($edition) {
            return $edition->id;
        } else {
            return 0;
        }
    }
}
