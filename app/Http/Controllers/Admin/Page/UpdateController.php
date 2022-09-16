<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Requests\Admin\Page\UpdateRequest;
use App\Models\Page;


class UpdateController extends BaseController
{
    public function __invoke(UpdateRequest $request, Page $page)
    {
        $data = $request->validated();
        $this->service->update($data, $page);
        return redirect()->route('admin.page.show', ['page' => $page->id]);
    }
}
