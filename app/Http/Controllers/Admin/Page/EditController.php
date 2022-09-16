<?php

namespace App\Http\Controllers\Admin\Page;

use App\Models\Page;

class EditController extends BaseController
{
    public function __invoke(Page $page)
    {
        $options = $this->service->getPageSelectOptions($page->parent_id, $page->id);
        return view('admin.page.edit', compact('page','options'));
    }
}
