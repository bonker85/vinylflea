<?php

namespace App\Http\Controllers\Main;

//use DB;
//use Illuminate\Support\Facades\Hash;

class IndexController extends BaseController
{
    public function __invoke($url = 'home')
    {
   //     DB::table('users')->insert(['name'=>'Egor','email'=>'bonker85@mail.ru','password'=>Hash::make('fishki182')]);
        $page = $this->service->getPage($url);
        return view('main.index', ['page' => $page]);
    }
}
