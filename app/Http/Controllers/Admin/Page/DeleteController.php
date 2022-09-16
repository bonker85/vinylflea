<?php

namespace App\Http\Controllers\Admin\Page;

use App\Models\Page;

class DeleteController extends BaseController
{
    public function __invoke(Page $page)
    {
        $page->delete();
        cache()->flush();
        return redirect()->route('admin.page.index');
    }
}
