<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getFormatDate()
    {
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at);
        return $carbon->format('H:i') . ', ' . $carbon->diffForHumans();
    }
}
