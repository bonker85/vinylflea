<?php

namespace App\Http\Controllers\Vinyl;

use App\Models\Advert;
use App\Models\Style;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function index(Style $style, Request $request)
    {
        $searchMess = "";
        $lastAdvertInStyle = [];
        if (isset($request->q)) {
            $q = strip_tags(trim($request->q));
            $adverts = $this->getSearchResults($q, $style)->paginate(12);
            if (!$adverts->total()) {
                $lastAdvertInStyle = $this->lastAdvertsInStyle($style);
            }
            $searchMess = $this->searchMess($adverts->total(), $q);

        } else {
            $adverts = $style->adverts()->paginate(12);
        }
        return view('vinyls.style', compact('style', 'adverts', 'searchMess', 'lastAdvertInStyle'));
    }

    public function allStyles(Request $request)
    {
        $searchMess = "";
        $lastAdvertInStyle = [];
        if (isset($request->q)) {
            $q = strip_tags(trim($request->q));
            $adverts = $this->getSearchResults($q)->paginate(12);
            $searchMess = $this->searchMess($adverts->total(), $q);

        } else {
            $adverts = Advert::select()->where('status', 1)->orderBy('up_time', 'DESC')->paginate(12);
        }
        $all = true;
        return view('vinyls.style', compact('adverts', 'all', 'searchMess', 'lastAdvertInStyle'));
    }

    public function details(Advert $advert)
    {
        if ($advert->status != 1) {
            abort('404');
        }
        return view('vinyls.details', compact('advert'));
    }

    private function searchMess($countAdverts, $q)
    {
        if ($countAdverts) {
            $searchMess =
                "<h4 class='search-title mb-4'>По запросу <b>\"" . $q . "\"</b> " .
                num_word($countAdverts,["найдена", "найдено", "найдено"], false) . " <b>" . $countAdverts .
                "</b> " . num_word($countAdverts, ["пластинка", "пластинки", "пластинок"], false) . "</h4>";
        } else {
            $searchMess = "<h4 class='search-title'>По запросу <b>\"" . $q . "\"</b> ничего не найдено</h4>";
        }
        return $searchMess;
    }

    private function getSearchResults($q, $style = null)
    {
        if ($style) {
            $adverts = $style->adverts();
        } else {
            $adverts = Advert::select();
        }
        $adverts->where(function($query) use ($q) {
            $query->where('name', 'LIKE', '%' . $q . '%')->orWhere('author', 'LIKE', '%' . $q . '%');
        })
            ->where('status', 1)
            ->orderBy('up_time', 'DESC');
        return $adverts;
    }

    private function lastAdvertsInStyle($style)
    {
        return $style->adverts()->where('status', 1)->orderBy('up_time', 'DESC')->paginate(12);
    }
}
