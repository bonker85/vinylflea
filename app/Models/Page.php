<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;
    protected $with = ['children'];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id', 'id')->orderBy('position');
    }
}
