<?php

namespace App\Http\Controllers\Profile;


use App\Services\Utility\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class IndexController extends BaseController
{
    public function index()
    {
        return view('profile.index');
    }

    public function settings()
    {
        if (request()->post()) {
            switch (request()->get('action')) {
                case 'info':
                    $this->service->editInfo();
                break;
                case 'email':
                    $this->service->editEmail();
                    break;
                case 'password':
                    $this->service->editPassword();
                    break;
            }
        }
        $cities = DB::table('city')->select('name')->orderBy('name')->get();
        $user = auth()->user();
        return view('profile.settings', compact('user', 'cities'));
    }

}
