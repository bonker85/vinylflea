<?php

namespace App\Http\Controllers\Vinyl;

use App\Models\Advert;
use App\Models\Style;

class IndexController extends BaseController
{
    public function index(Style $style)
    {
        $adverts = $style->adverts()->paginate(12);
        return view('vinyls.style', compact('style', 'adverts'));
    }

    public function allStyles()
    {
        $adverts = Advert::select()->where('status', 1)->orderBy('up_time', 'DESC')->paginate(12);
        $all = true;
        return view('vinyls.style', compact('adverts', 'all'));
    }

    public function details(Advert $advert)
    {
        if ($advert->status != 1) {
            abort('404');
        }
        return view('vinyls.details', compact('advert'));
    }
}
