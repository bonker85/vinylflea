<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AyBy extends Model
{
    use HasFactory;
    const TYPES = [1 => 'rok', 2 => 'pop', 3 => 'estrada', 4 => 'bardy', 5 => 'dzhaz', 6 => 'shanson', 7 => 'drugoe'];
//    const TYPES = [7 => 'drugoe'];
    protected $table = 'ay_by';
    protected $guarded = false;

}
