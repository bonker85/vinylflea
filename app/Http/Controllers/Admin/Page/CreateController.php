<?php

namespace App\Http\Controllers\Admin\Page;


class CreateController extends BaseController
{
    public function __invoke()
    {
       $options = $this->service->getPageSelectOptions(request()->old('parent_id'));
       return view('admin.page.create', compact('options'));
    }
}
