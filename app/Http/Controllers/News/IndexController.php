<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class IndexController extends Controller
{


    public function index(Page $page = null)
    {
        if (is_null($page)) {
            $title = "Новости";
            $newsList = Page::select()->where('parent_id', 2)->where('status', 1)->orderBy('position')->get();
            return view('news.index', compact('newsList', 'title'));
        } else if ($page) {
            $new = $page;
            $title = $page->title;
            return view('news.details', compact('new', 'title'));
        }

    }
}
