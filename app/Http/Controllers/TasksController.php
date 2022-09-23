<?php

namespace App\Http\Controllers;

use App\Models\CanvasSize;
use App\Models\Catalog;
use App\Models\Color;
use App\Models\Door;
use App\Models\User;
use App\Services\DoorService;
use App\Services\Utility\WatermarkService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class TasksController extends Controller
{

    private $log;
    private $service;

    public function index(Request $request, $param)
    {
        set_time_limit(100000);
        switch($param) {
            case 'toggle_user':
                if ($request->post('id')) {
                    $user = User::find($request->get('id'));
                    auth()->login($user);
                    return redirect()->route('profile.settings');
                }
                break;
            default:
                abort('404');
                break;
        }
    }

    private function parseProductsPage($html, $link, $catalog, $pages = 0) {
        $this->log->info('Парсинг каталога ' . $catalog->name . ', страница ' . ($pages+1));
        $doorService = new DoorService();
        $doc1 = new DOMDocument();
        @$doc1->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $html);
        $xpath1 = new DOMXPath($doc1);
        $productLists = $xpath1->query("//div[@class='catalog-product-card-inner']");
        $productLinkBlocks = $xpath1->query("//div[@class='catalog-roduct-hover-block']/a[contains(@class,
        'catalog-product-card-order-button')]");
        if($productLists->length && ($productLists->length === $productLinkBlocks->length)) {
            $this->log->info('На странице найдено ' . $productLists->length);
            $item_num = 0;
            foreach ($productLists as $product) {
                $productTitle =
                    trim($xpath1->query('div/p[@class="catalog-product-card-title"]', $product)->item(0)->nodeValue);
                $collection =
                    trim($xpath1->query('div/p[@class="catalog-product-card-subtitle"]', $product)->item(0)->nodeValue);
                $productPurchasePrice =
                    trim($xpath1->query('p[@class="catalog-product-card-hover-price"]', $product)->item(0)->nodeValue);

                $productPurchasePrice = str_replace(['₽', "&nbsp;",' ', chr(194) . chr(160)], '', $productPurchasePrice);
                $door = Door::select()->where('name', $productTitle)->where('catalog_id', $catalog->id)->first();
                if ($door) {
                    $this->log->info('Продукт ' . $door->name . ' уже существует, обновляем цену закупки '
                        . $productPurchasePrice);
                    $door->purchase_price = $productPurchasePrice;
                    $door->save();
                    $item_num++;
                } else {
                    $productLink = $productLinkBlocks->item($item_num)->getAttribute('href');
                    $this->log->info('Добавление продукта ' . $productTitle .
                    '(' . $productLink . ')');
                    $item_num++;
                    $productContent = file_get_contents($productLink);
                    if ($productContent) {
                        $doc2 = new DOMDocument();
                        @$doc2->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' .
                            $productContent);
                        $xpath2 = new DOMXPath($doc2);
                        $mainImage =
                            $xpath2->query("//meta[contains(@property, 'og:image')]")->item(0)->getAttribute('content');
                        $originalDir = $doorService->getOriginalDoorDir(['catalog_id' => $catalog->id,
                            'name' => $productTitle]);
                        $fileExt = pathinfo($mainImage, PATHINFO_EXTENSION);
                        $fileName = Str::slug(pathinfo($mainImage, PATHINFO_FILENAME));
                        $fileFullName = $fileName . '.' . $fileExt;
                        $originalImagePath = $originalDir . $fileFullName;
                        if (!Storage::disk('public')->put("/" . $originalImagePath, file_get_contents($mainImage))) {
                            $this->log->error('Не удалось загрузить главное изображение продукта (' . $mainImage . ')');
                            dd("Не удалось загрузить главное изображение продукта " . $mainImage);
                        } else {
                            $mainImage = $this->service->addDoorImagesByOriginalPath($originalImagePath);
                        }
                        $characteristics = $xpath2->query("//table[@class='table table-striped']");
                        if ($characteristics) {
                            $names = $xpath2->query("//table[@class='table table-striped']/tbody/tr/th");
                            $values = $xpath2->query("//table[@class='table table-striped']/tbody/tr/td");
                            if ($names->length === $values->length || $names->length === ($values->length+1)) {
                                $characteristics = '<table class="table table-hover text-nowrap"><tbody>';
                                for($i=0;$i<$values->length;$i++) {
                                    $characteristics .= '<tr><th>' . trim($names->item($i)->nodeValue) . '</th>';
                                    $characteristics .= '<td>' . trim($values->item($i)->nodeValue) . '</td></tr>';
                                }
                                $characteristics.= '</tbody></table>';
                            }
                        } else {
                            $characteristics = '<table><tbody><tr><th></th><td></td></tr></tbody></table>';
                        }
                        $canvasSizes = $xpath2->query("//span[contains(@class,'variable-item-span')]");
                        if ($canvasSizes->length) {
                            $canvasSizesIds = [];
                            foreach ($canvasSizes as $size) {
                                $size = trim($size->nodeValue);
                                $size = CanvasSize::firstOrCreate(['size' => $size], ['size' => $size]);
                                $canvasSizesIds[] = $size->id;

                            }
                        } else {
                            $this->log->error('Не удалось загрузить размеры полотна (' . $productLink . ')');
                        }
                    } else {
                        $this->log->error('Не удалось загрузить html страницы (' . $productLink . ')');
                    }
                    $colors = $xpath2->query("//div[@class='variable-item-contents']/img");
                    if ($colors->length) {
                        $colorLists = [];
                        foreach ($colors as $color) {
                            $colorName = trim($color->getAttribute('alt'));
                            $colorImg = trim($color->getAttribute('data-src'));
                            if ($colorImg) {
                                $fileExt = pathinfo($colorImg, PATHINFO_EXTENSION);
                                $fileName = Str::slug(pathinfo($colorImg, PATHINFO_FILENAME));
                                $fileFullName = $fileName . '.' . $fileExt;
                            } else {
                                $fileFullName = '';
                            }

                            $color = Color::select()->where('name', $colorName)->first();
                            if (!$color) {
                                if ($fileFullName) {
                                    $colorPath = 'colors/' . $fileFullName;
                                    $path = Storage::disk('public')->
                                    put($colorPath, file_get_contents($colorImg));
                                } else {
                                    $path = 1;
                                    $colorPath = '';
                                }
                                if ($path) {
                                    $color = Color::create([
                                        'name' => $colorName,
                                        'image' => $colorPath
                                    ]);
                                } else {
                                    $this->log->error('Изображение цвета ' . $colorName . ' не загружено в Storage '
                                        . $path);
                                    dd('Изображение цвета ' . $colorName . ' не загружено в Storage ' . $colorPath);
                                }
                            }
                            $colorLists[] = $color->id;
                        }
                    } else {
                        $this->log->error('Не удалось получить цвета продукта');
                    }
                    $url = $this->doorUrlCreate($productTitle, $catalog);

                    $doorInfo = [
                        'name' => $productTitle,
                        'url' => $url,
                        'header' => $productTitle,
                        'title' => $catalog->parent->name . ' | ' .
                            $catalog->name . ' | ' . $productTitle,
                        'description' => 'Купить ' . $catalog->parent->name . ', ' . $catalog->name . ', ' .
                             $productTitle . ' недорого',
                        'characteristics' => $characteristics,
                        'status' => 1,
                        'catalog_id' => $catalog->id,
                        'new' => 0,
                        'discount' => 0,
                        'price' => 0,
                        'purchase_price' => $productPurchasePrice,
                        'discount_price' => 0,
                        'main_image' =>  $mainImage,
                        'position' => $doorService->getMaxPosition($catalog->id),

                    ];
                    $door = Door::create($doorInfo);
                    if (isset($canvasSizesIds)) {
                        $door->sizes()->sync($canvasSizesIds);
                    }
                    if (isset($colorLists)) {
                        $door->colors()->sync($colorLists);
                    }
                }

            }
            if (!$pages) {
                $pageCount = $xpath1->query("//ul[@class='page-numbers']/li");
                if ($pageCount->length) {
                    $pageCount = $pageCount->length - 1;
                    for ($i=2;$i<=$pageCount;$i++) {
                        $pageContent = file_get_contents($link . 'page/' . $i);
                        if ($pageContent) {
                            $this->parseProductsPage($pageContent, $link, $catalog, $i-1);
                        } else {
                            $this->log->error('Не удалось получить html страницы пагинации (' .
                                $link . 'page/' . $i . ')');
                            dd('Не удалось получить html страницы пагинации (' . $link . 'page/' . $i . ')');
                        }
                    }
                }
            }


        }

    }

    private function doorUrlCreate($name, $catalog, $counter = 0) {
        if ($counter !== 0) {
            $name = $name . '-' . $counter;
        }
        $url = translate_url($name);
        if ($this->isUrlExists($url, $catalog->id)) {
            $counter++;
            return $this->doorUrlCreate($name, $catalog, $counter);
        }
        return $url;

    }

    private function isUrlExists($url, $catalog_id) {
        $doors = Door::select()->where('url', $url)->where('catalog_id', '!=', $catalog_id)->get();
        return $doors->count();
    }
}

