<?php

namespace App\Http\Controllers\Admin\Page;


use App\Models\Page;

class DestroyController extends BaseController
{
    public function __invoke($pages)
    {
        $pages_array = explode(',', $pages);
        Page::destroy($pages_array);
        cache()->flush();
        return redirect()->route('admin.page.index');
    }
}
