<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\DiscogsArtist;
use App\Models\Edition;
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
        $artists = DiscogsArtist::select('discogs_artist_id')->where('discogs_artist_id', '!=', 194)->get();
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

    public function gfeed()
    {
        $out = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
        $out .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\r\n";
        $out .= '<channel>' . "\r\n";
        $out .= '<title>VinylFlea.By</title>' . "\r\n";
        $out .= '<description>VinylFlea – это сайт-барахолка, предназначенный для обмена, продажи, а также безвозмездной передачи виниловых пластинок из рук в руки. Наш сайт создан для любителей раритета, хорошего звука, меломанов, а также людей получающих эстетическое удовольствие от самого процесса проигрывания пластинки.</description>' . "\r\n";
        // URL главной страницы магазина
        $out .= '<link>https://example.com/</link>' . "\r\n";
        $adverts = Advert::select()
            ->where('status', 1)
            ->where('deal', 'sale')
            ->where('price', '>', 0)
            ->whereIn('user_id', [4, 6, 11])->get();
        foreach ($adverts as $advert) {
            $out .= '<item>' . "\r\n";

            // ID товара
            $out .= '<g:id>' . $advert->id . '</g:id>' . "\r\n";

            // Название товара
            $out .= '<title>' . $advert->name . '</title>' . "\r\n";

            // URL страницы товара на сайте магазина
            $out .= '<link>' . route('vinyls.details', $advert->url) . '</link>' . "\r\n";

            // Описание товара
            $out .= '<g:description><![CDATA['. htmlspecialchars($advert->description) . ']]></g:description>' . "\r\n";

            // Цена
            $out .= '<g:price>' . $advert->price . ' BYN</g:price>' . "\r\n";

            // Цена со скидкой
            $out .= '<g:sale_price>' . $advert->price . ' BYN</g:sale_price>' . "\r\n";

            // Изображения товара
            if (count($advert->images)) {
                foreach ($advert->images as $image) {
                    $out .= '<g:image_link>' .  cdn_url(asset('/storage' . $image->path), $image) .  '</g:image_link>' . "\r\n";
                }
            }

            // Производитель
            if ($advert->edition_id) {
                $edition = Edition::find($advert->edition_id)->name;
            } else {
                $edition = '';
            }
            if ($edition) {
                $out .= '<g:brand>' . $edition . '</g:brand>' . "\r\n";
            }
            $out .= '<g:availability>in stock</g:availability>' . "\r\n";
            $out .= '<g:google_product_category>Виниловые пластинки > Стиль: ' . $advert->style->name . '</g:google_product_category>' . "\r\n";

            // Стоимость и срок доставки
            $out .= '
                <g:shipping>
                    <g:country>BY</g:country>
                    <g:price>' .$advert->price . ' BYN</g:price>
                    <g:min_handling_time>1</g:min_handling_time>
                    <g:max_handling_time>3</g:max_handling_time>
                    <g:min_transit_time>1</g:min_transit_time>
                    <g:max_transit_time>3</g:max_transit_time>
                </g:shipping>';

            $out .= '</item>' . "\r\n";
        }
        $out .= '</channel>' . "\r\n";

// Вывод в браузер
        header('Content-Type: text/xml; charset=utf-8');
        echo $out;exit();
    }

}

