<?php

namespace App\Http\Controllers\Admin\Page;

use App\Models\Page;

class ShowController extends BaseController
{
    public function __invoke(Page $page)
    {
        if ($page->parent_id) {
            $parent_name = $page->parent->name;
        } else {
            $parent_name = "Корневая";
        }
        return view('admin.page.show', compact('page', 'parent_name'));
    }
}
