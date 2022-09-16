<?php

namespace App\Http\Controllers\Admin\Page;

use App\Models\Page;

class IndexController extends BaseController
{
    public function __invoke()
    {
        $pages= Page::all();
        return view('admin.page.index', compact('pages'));
    }
}
