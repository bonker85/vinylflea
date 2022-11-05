<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\DiscogsArtist;
use App\Models\Page;
use App\Models\Style;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use function Termwind\render;

class SitemapController extends Controller
{

    public function index(Request $request)
    {
        set_time_limit(100000);
        $lastModificationDate = Carbon::yesterday();
        $sitemap = Sitemap::create();
        //добавление главной
        $sitemap->add(Url::create('/')->setLastModificationDate($lastModificationDate));
        //добавление артистов
        $artists = DiscogsArtist::select('discogs_artist_id')->get();
        foreach ($artists as $artist) {
            $sitemap->add(Url::create(route('artist', $artist->discogs_artist_id))->setLastModificationDate($lastModificationDate));
        }
        //Добавление новостей
        $sitemap->add(Url::create('/news')->setLastModificationDate($lastModificationDate));
        $pages = Page::select('url')->where('status', 1)->where('parent_id', 2)->get();
        foreach ($pages as $page) {
            $sitemap->add(Url::create('/news/' . $page->url)->setLastModificationDate($lastModificationDate));
        }
        //Добавление стилей
        $sitemap->add(Url::create(route('vinyls.styles'))->setLastModificationDate($lastModificationDate));
        $styles = Style::select('slug')->get();
        foreach ($styles as $style) {
            $sitemap->add(Url::create(route('vinyls.style', $style->slug))
                ->setLastModificationDate($lastModificationDate));
        }
        //Добавление объявлений
        $adverts = Advert::select('url')->where('status', 1)->get();
        foreach ($adverts as $advert) {
            $sitemap->add(Url::create(route('vinyls.details', $advert->url))
                ->setLastModificationDate($lastModificationDate));
        }
        return $sitemap->render();
    }

}

