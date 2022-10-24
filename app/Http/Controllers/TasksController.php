<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\CanvasSize;
use App\Models\Catalog;
use App\Models\Color;
use App\Models\Door;
use App\Models\Log as DbLog;
use App\Models\Edition;
use App\Models\Style;
use App\Models\User;
use App\Services\DoorService;
use App\Services\Utility\CDNService;
use App\Services\Utility\ImageService;
use App\Services\Utility\WatermarkService;
use DOMDocument;
use DOMXPath;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


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
            case 'toggle_admin':
                if ($request->get('user_id')) {
                    $user = User::find($request->get('user_id'));
                    auth()->login($user);
                    return redirect()->route('profile.adverts');
                }
            case 'seeds':
                $users = [4, 5];
                $styles = Style::select()->pluck('id')->toArray();
                $editions = Edition::select()->pluck('id')->toArray();
                for ($i=0;$i<1000;$i++) {
                    $price = ['11.00', '2', '33', '3.03', '5.55', '244.33', '88', '77', '666', '328.01', '328.03'][rand(0,10)];
                    $randStyleId = $styles[rand(0,60)];
                    $randUserId = $users[rand(0,1)];
                    $randEditionId = $editions[rand(0,169)];
                    $stateId = [1,2][rand(0,1)];
                    $statusId = [1,2,3,4][rand(0,3)];
                    $faker = \Faker\Factory::create();
                    $time = $faker->date . ' ' . $faker->time;
                    $year = $faker->year;
                    $name = $faker->sentence();
                    $url = translate_url($name);
                    $description = $faker->paragraph;
                    Advert::create([
                        'name' => $name,
                        'url' => $url,
                        'description' => $description,
                        'price' => $price,
                        'style_id' => $randStyleId,
                        'edition_id' => $randEditionId,
                        'user_id' => $randUserId,
                        'year' => $year,
                        'state' => $stateId,
                        'status' => $statusId,
                        'up_time' => $time,
                        'created_at' => $time,
                        'updated_at' => $time
                    ]);
                }

                dd('FIN');
                break;
            case 'create_slug_for_styles':
                $styles  = Style::select()->where('slug', '')->get();
                foreach ($styles as $style) {
                    $style->slug = translate_url($style->name);
                    $style->save();
                }
                dd('FIN');
                break;
            case 'create_ad_thumb':
                $imageService = new ImageService();
                $advertImages = AdvertImage::select()
                    ->where('thumb', 0)->where('path','LIKE', '%vinyl1%')->get();
                $time = time();
                foreach ($advertImages as $image) {
                    $filePath = storage_path('app/public') . $image->path;
                   if (file_exists($filePath)) {
                        $fileThumbPath =
                            storage_path('app/public') .
                            str_replace('/users/',
                                '/advert_thumbs/', $image->path);
                        $imageService->createImageThumbnail($filePath, $fileThumbPath);
                        $image->thumb = 1;
                        $image->thumb_update_time = $time;
                        $image->save();
                    }
                }
                dd('FIN');
                break;
            case 'parser-vinil-sd-by':
              /*  $adverts = Advert::select('description','id')->where('user_id', 6)->get();
                foreach ($adverts as $advert) {
                    $advert->description = '<b>Наличие уточняйте</b><br/>' . $advert->description;
                    $advert->save();
                }*/
                dd('FIN');
                $this->log = Log::channel('parser-vinil-sd-by');
                $siteUrl = 'https://store.tildacdn.com/api/getproductslist/?storepartuid=495183118261&recid=375529451&c=1665522162862&getparts=true&getoptions=true&slice=9&size=500';

              /*  $parseConfig = [
                    'mezhkomnatnye-dveri' => [
                        'menu_id' => 'dropdown-1'
                    ],
                    'vhodnye-dveri' => [
                        'menu_id' => 'dropdown-2'
                    ]
                ];*/
                $data = json_decode(file_get_contents($siteUrl));
                $products = $data->products;
                foreach ($products as $product) {
                    if (Advert::where('url', translate_url($product->title) . '-' . $product->uid)->first()) {
                        continue;
                    }
                    $data = [
                        'name' => $product->title,
                        'author' => '',
                        'url' => translate_url($product->title) . '-' . $product->uid,
                        'description' => $product->descr,
                        'price' => $product->price,
                        'style_id' => 1,
                        'user_id' => 6,
                        'deal' => 'sale',
                        'state' => 2,
                        'condition' => trim(str_replace('Состояние (пластинки/конверта)', '', $product->text)),
                        'status' => (($product->quantity) ? 2: 4),
                        'reject_message' => '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $advert = Advert::firstOrCreate(['url' => $data['url']],$data);
                    $images = json_decode($product->gallery);
                    if ($images) {
                        $imageService = new ImageService();
                        ///users/6/1005/vinyl4.jpg
                        $i = 1;
                        foreach ($images as $image) {
                            if (@file_get_contents($image->img)) {
                                if ($i >= 4) break;
                                $ext = '.' . pathinfo($image->img, PATHINFO_EXTENSION);
                                $path = public_path('storage') .  '/users/6/' . $advert->id . '/vinyl' . $i . $ext;
                                if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                                    $img = Image::make($image->img);
                                    $img->resize(800, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    })->save($path);
                                    $imageService->createImageWatermark(
                                        $path,
                                        $path,
                                        public_path('images/watermarks/watermark.png')
                                    );
                                    //'/users/6/' . $advert->id . '/vinyl.jpg',
                                    AdvertImage::firstOrCreate(
                                        ['path' => $path],
                                        [
                                            'advert_id' => $advert->id,
                                            'path' => str_replace('public\storage', '',
                                                substr($path, strpos($path, 'public\storage')))
                                        ]);
                                }
                                $i++;
                            }

                        }
                    }

                  /*  if ($id &&
                        $imageService->isImage(request()->file($fileName))) {
                        if ($imageService->isFileMoreSize(request()->file($fileName)->getSize())) {
                            $error = 'Файл не должен превышать ' . format_size(env('MAX_FILE_SIZE'));
                        } else {
                            if ($imageService->createTmpImage(request()->file($fileName), $fileName)) {
                                $userId = auth()->user()->id;
                                $ext = strtolower(request()->file($fileName)->getClientOriginalExtension());
                                return [
                                    'error' => $error,
                                    'url' => asset('storage/tmp/' . $userId . '/' . $fileName . '.' . $ext . '?t=') . time()
                                ];
                            } else {
                                $error = "Произошла ошибка при загрузке файла";
                            }
                        }
                    }*/
                }
              /*  if ($siteMainPageContent) {
                    $doc = new DOMDocument();
                    @$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $siteMainPageContent);
                    $xpath = new DOMXPath($doc);
                    $this->log->info('Начало парсинга');
                    foreach ($parseConfig as $menu_name => $config) {
                        $this->log->info('Парсинг раздела ' . $menu_name);
                        $subMenuLinks =
                            $xpath->query("//div[@id='" . $config['menu_id'] . "']/a[@class='dropdown-item']");
                        if ($subMenuLinks->length) {
                            foreach ($subMenuLinks as $link) {
                                $linkSubDoors = trim($link->getAttribute('href'));
                                //$linkSubDoors = 'https://dveri-vdk.ru/category/mezhkomnatnye-dveri/enamel-emal-belaya/';
                                $subDoorName = trim($link->nodeValue);
                                $this->log->info('Категория: ' . $subDoorName . ' | Url: ' . $linkSubDoors);
                                $catalog = Catalog::select()->where('name', $subDoorName)->first();
                                if (!$catalog) {
                                    $this->log->error('Каталог ' . $subDoorName . ' не найден в базе!');
                                    dd('Каталог ' . $subDoorName . ' не найден в базе');
                                }
                                $pageContent = file_get_contents($linkSubDoors);
                                if ($pageContent) {
                                    $this->parseProductsPage($pageContent, $linkSubDoors, $catalog);
                                    $this->log->info('Конец парсинга каталога ' . $catalog->name);
                                    $this->log->info('-------------------------------------------------------');
                                } else {
                                    $this->log->error('Не удалось получить html страницы url ' . $linkSubDoors);
                                }
                            }
                        } else {
                            $this->log->error('Не удалось выпарсить меню ' . $menu_name);
                        }

                    }
                    echo 'Fin';exit();
                } else {
                    $this->log->error('Не удалось получить html главной страницы сайта ' . $siteUrl);
                }*/
                dd('FIN');
                break;
            case 'sync_cdn':
                $cdnService = new CDNService();
                //update user avatar images
                $userAvatarImages = User::select("id", "avatar", "cdn_status")
                                            ->where("avatar", "!=", "")
                                            ->where("cdn_status", 0)
                                            ->get();
                $now = now();
                $time = time();
                foreach ($userAvatarImages as $uImage) {
                    $path = $uImage->avatar;
                    $filePath = storage_path('app/public') . $path;
                    if (file_exists($filePath)) {
                        $storagePath =  $path;
                        $res = $cdnService->uploadFile($filePath, $storagePath);
                        if (!$res["error"]) {
                            $uImage->cdn_status = 1;
                            $uImage->cdn_update_time = $time;
                            $uImage->save();
                        } else {
                            DbLog::insert([
                                'type' => DbLog::TYPES['cdn_error_update_avatar'] ,
                                'message' => 'Send Request Error: UserId' . $uImage->id . ", Body Output:" . $res['body'],
                                'created_at' => $now,
                                'updated_at' => $now
                            ]);
                        }
                    } else {
                        DbLog::insert([
                            'type' => DbLog::TYPES['cdn_error_update_avatar'] ,
                            'message' => 'File Exist Error: UserId ' . $uImage->id . ', ' .
                                "avatar image don't exist on disc, path: " . $uImage->avatar,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }
                //update advert avatar images
                $advertImages = AdvertImage::select("id", "path", "cdn_status")
                    ->where("cdn_status", 0)
                    ->get();
                foreach ($advertImages as $aImage) {
                    $path = $aImage->path;
                    $filePath = storage_path('app/public') . $path;
                    if (file_exists($filePath)) {
                        $storagePath =  $path;
                        $res = $cdnService->uploadFile($filePath, $storagePath);
                        if (!$res["error"]) {
                            $aImage->cdn_status = 1;
                            $aImage->cdn_update_time = $time;
                            $aImage->save();
                        } else {
                            DbLog::insert([
                                'type' => DbLog::TYPES['cdn_error_update_advert'] ,
                                'message' => 'Send Request Error: AdvertImageId' . $aImage->id . ", Body Output:" . $res['body'],
                                'created_at' => $now,
                                'updated_at' => $now
                            ]);
                        }
                    } else {
                        DbLog::insert([
                            'type' => DbLog::TYPES['cdn_error_update_advert'] ,
                            'message' => 'File Exist Error: AdvertImageId ' . $aImage->id . ', ' .
                                "image don't exist on disc, path: " . $aImage->path,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }
                dd("FIN");
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

