<?php

namespace App\Http\Controllers\Main;

//use DB;
//use Illuminate\Support\Facades\Hash;

use App\Models\Advert;
use App\Models\Page;
use App\Models\Style;
use App\Services\AdvertService;
use App\Services\ProfileService;


class IndexController extends BaseController
{
    public function __invoke($url = 'home')
    {
     //   $profileSevert = new ProfileService();
      // dd($profileSevert->createAdvertThumbnail('/rule/main.jpg'));
   //     DB::table('users')->insert(['name'=>'Egor','email'=>'bonker85@mail.ru','password'=>Hash::make('fishki182')]);
        $page = $this->service->getPage($url);
        $lastAdvertsList = Advert::select()
            ->where('status', AdvertService::getStatusByName('activated'))
            ->orderBy('up_time', 'DESC')->limit(12)->get();
        $styles = Style::select()->orderBy('name')->get();
        return view('main.index', compact('page', 'lastAdvertsList', 'styles'));
    }

}
