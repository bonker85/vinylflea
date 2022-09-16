<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Requests\Admin\Page\StoreRequest;
//use Illuminate\Http\Request;


class StoreController extends BaseController
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();
        $this->service->store($data);
        return redirect()->route('admin.page.index');
    }
}
