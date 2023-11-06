<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\CanvasSize;
use App\Models\Catalog;
use App\Models\Color;
use App\Models\DiscogsArtist;
use App\Models\Door;
use App\Models\Edition;
use App\Models\Kufar;
use App\Models\Phone;
use App\Models\Style;
use App\Models\User;
use App\Services\AdvertService;
use App\Services\DoorService;
use App\Services\Utility\DiscogsService;
use App\Services\Utility\GoogleTranslateService;
use App\Services\Utility\ImageService;
use App\Services\Utility\VkService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Xyrotech\Orin;


class TasksController extends Controller
{

    private $log;
    private $service;

    public function index(Request $request, $param)
    {
        set_time_limit(100000);
        switch($param) {
            /**
             * Авторизация под конкретным юзером
             */
            case 'toggle_user':
                if ($request->get('id')) {
                    $user = User::find($request->get('id'));
                    auth()->login($user);
                    return redirect()->route('profile.settings');
                }
                break;
            case 'seeds':
                /**
                 * Заполнение базы левыми данными (уже не надо)
                 */
                exit();
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
                /**
                 * Заполнение таблицы стилей алиасами (уже не надо)
                 */
                exit();
                $styles  = Style::select()->where('slug', '')->get();
                foreach ($styles as $style) {
                    $style->slug = translate_url($style->name);
                    $style->save();
                }
                dd('FIN');
                break;
            case 'count_styles':
                AdvertService::recountStylesAdverts();
                dd("FIN");
                break;
            case 'create_ad_thumb':
                AdvertService::createAdThumb();
                dd('FIN');
                break;
            case 'create_excel':
              /*  return (new UserAdvertsExport(4))->download('vinyl.xlsx');
                break;*/
            case 'parser-kma':
                $list = file_get_contents('list.txt');
                $rows = preg_split('/\\r\\n?|\\n/', $list);
                $bucks = "2.5";
                $params = [
                    "format" => "Vinyl",
                    "type" => "release"
                ];
                $imageService = new ImageService();
                $discogs = new Orin(Config::get('discogs'));
                foreach ($rows as $row) {
                    if (empty($row)) continue;
                   // $row = 'Various - The Many Faces Of Iron Maiden (A Journey Through The Inner World Of Iron Maiden) 2LP 2020new 50.0';
                    if (preg_match("#(.+)\s([EX|VG|NM].+?)\s(.+?)$#is", $row, $pockets)) {
                        $search = $pockets[1];
                        $doubleSearch = Db::table('discogs_search')->where('name', $search)->first();
                        if ($doubleSearch) {
                            continue;
                            echo "Запрос " . $search . " уже существует в базе";exit();
                        }
                        $condition = $pockets[2];
                        $price = (int) str_replace('.0', '', $pockets[3]) * $bucks;
                        $searchRelease = $discogs->search($search, $params);
                        $description = '';
                        if ($searchRelease->status_code !== 200) {
                            echo 'status code !== 200';exit();
                        } else if (isset($searchRelease->results[0])) {
                            $result = $searchRelease->results[0];
                            if (isset($result->cover_image) && !empty($result->cover_image)) {
                               $imageUrl = $result->cover_image;
                            } else {
                                echo "Для запроса " . $search . " не найден cover_image";exit();
                            }
                            if (isset($result->title) && !empty($result->title)) {
                                $name = $result->title;
                            }  else {
                                echo "По запросу " . $search . " не найдено название";exit();
                            }
                            if (isset($result->genre[0])) {
                                $description .= "Жанр: " . implode(', ', $result->genre) . "\r\n";
                            } else {
                                $description .= "Жанр: - \r\n";
                            }
                            if (isset($result->style[0])) {
                                $style = Style::select()->where('name', $result->style[0])->first();
                                if ($style) {
                                    $style_id = $style->id;
                                } else {
                                    $style_id = 66;
                                    echo "Style = " . $result->style[0] . " для запроса: " . $search . " не найден";
                                }
                                $description .= "Стиль: " . implode(', ', $result->style) . "\r\n";
                            } else {
                                $style_id = 66;
                                $description .= "Стиль: - \r\n";
                            }
                            $releaseId = $result->id;
                            $release = $discogs->release($releaseId);
                            if ($release->status_code !== 200) {
                                echo 'status code !== 200';
                                exit();
                            } elseif (isset($release->artists[0])) {
                                if (isset($release->title) && !empty($release->title)) {
                                    $name = $release->title;
                                }
                                $artists = [];
                                foreach ($release->artists as $artist) {
                                    $artists[] = $artist->name;
                                }
                                $author = implode(", ", $artists);
                                if (mb_strlen($author) > 80) {
                                    $author = mb_substr($author, 0, 70) . '...';
                                }
                            } else {
                                echo "По запросу " . $search . " не найден автор";exit();
                            }
                            $userId = 22;
                            $state = 2;
                            if (trim($condition) == 'NM/NM') {
                                $state = 1;
                            }
                            $data = [
                                'name' => $name,
                                'author' => $author,
                                'discogs_author_ids' => 0,
                                'url' => translate_url($name) . '-t' . time(),
                                'description' => $description,
                                'price' => $price,
                                'style_id' => $style_id,
                                'edition_id' => 0,
                                'user_id' => $userId,
                                'deal' => 'sale',
                                'state' => $state,
                                'condition' => $condition,
                                'status' => 3, //rejected
                                'reject_message' => '',
                                'cron' => 0,
                                'sku' => 0,
                                'uid' => 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $advert = Advert::firstOrCreate(['url' => $data['url']],$data);
                            $advert->url = preg_replace("#(-t\d.+)#is", "-a" . $advert->id, $advert->url);
                            $advert->save();
                            if (@file_get_contents($imageUrl)) {
                                $ext = '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                                $userPath = '/users/' . $userId . '/' . $advert->id . '/vinyl1' . $ext;
                                $path = public_path('storage') .  $userPath;
                                if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                                    $img = Image::make($imageUrl);
                                    $img->resize(500, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    })->save($path);
                                    $imageService->createImageWatermark(
                                        $path,
                                        $path,
                                        public_path('images/watermarks/watermark.png')
                                    );
                                    //'/users/6/' . $advert->id . '/vinyl.jpg',
                                    AdvertImage::firstOrCreate(
                                        ['path' => $userPath],
                                        [
                                            'advert_id' => $advert->id,
                                            'path' => $userPath
                                        ]);
                                }
                            }
                            Db::table('discogs_search')->insert(['name' => $search]);
                            sleep(1);
                        } else {
                            echo "По запросу " . $search . " ничего не найдено";exit();
                        }
                    } else {
                        dd($row);
                        echo "________________________END NORM___________" . "<br/>";
                        dd($row);
                        echo $row . "<br/><br/>";
                    }
                }
                echo 'abahaba';exit();
                break;
            case 'parser-vinil-sd-by':
                $this->log = Log::channel('parser-vinil-sd-by');
                /**
                 * Парсер с сайта vinil-sd.by
                 */
                Advert::where('user_id', 6)->update(['hide_advert' => 1]);
                $show_advert = 0;
                for ($j=1; $j<=13; $j++) {
                    $slice = $j;
                    $siteUrl = 'https://store.tildacdn.com/api/getproductslist/?storepartuid=495183118261&recid=375529451&c=1665522162862&getparts=true&getoptions=true&slice=' . $slice . '&size=500';

                    $data = json_decode(file_get_contents($siteUrl));
                    $products = $data->products;
                    $this->log->info('__PROHOD__', ['j' => $j]);
                    if (!count($products)) {
                        break;
                    }
                    foreach ($products as $product) {
                        if (!isset($product->sku) || empty($product->sku)) {
                            //для продукта ни артикул ни uid не определен, ничего с ним не делаем
                            continue;
                        } else {
                            $advert = Advert::select()->where('user_id', 6)->where('sku', $product->sku)->first();
                        }
                        if ($advert) {
                          /*  $this->log->info('__UPDATE__', [
                                'sku' => $product->sku,
                                'text' => $product->text,
                                'condition' => trim(str_replace('Состояние (пластинки/конверта)', '', $product->text))]);
                          */
                            // обновление статуса
                            // Если есть в наличии и в статусе скрыт
                            if ($product->quantity && $advert->status == 4) {
                                //кидаем на модерацию
                                $advert->status = 2;
                                //если нет в нали, переводим в статус скрыт
                            } else if (!$product->quantity && (int)$advert->status === 1) {
                                $advert->status = 4;
                            }
                            $advert->sku = $product->sku;
                            $advert->price = $product->price;
                            $advert->condition =
                                trim(str_replace('Состояние (пластинки/конверта)', '', $product->text));
                            $advert->hide_advert = 0;
                            $show_advert++;
                            $advert->save();
                        } else {
                            $data = [
                                'name' => $product->title,
                                'author' => '',
                                'discogs_author_ids' => 0,
                                'url' => translate_url($product->title) . '-' . $product->uid,
                                'description' => '<b>Наличие уточняйте</b><br/>' . $product->descr,
                                'price' => $product->price,
                                'style_id' => 1,
                                'edition_id' => 0,
                                'user_id' => 6,
                                'deal' => 'sale',
                                'state' => 2,
                                'condition' => trim(str_replace('Состояние (пластинки/конверта)', '', $product->text)),
                                'status' => (($product->quantity) ? 2: 3),
                                'reject_message' => '',
                                'cron' => 0,
                                'sku' => $product->sku,
                                'uid' => $product->uid,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $advert = Advert::firstOrCreate(['url' => $data['url']],$data);
                            $advert->hide_advert = 0;
                           // $show_advert++;
                            $advert->save();
                            $images = json_decode($product->gallery);
                            if ($images) {
                                $imageService = new ImageService();
                                ///users/6/1005/vinyl4.jpg
                                $i = 1;
                                foreach ($images as $image) {
                                    if (@file_get_contents($image->img)) {
                                        if ($i >= 4) break;
                                        $ext = '.' . pathinfo($image->img, PATHINFO_EXTENSION);
                                        $userPath = '/users/6/' . $advert->id . '/vinyl' . $i . $ext;
                                        $path = public_path('storage') .  $userPath;
                                        if (make_directory(pathinfo($path)['dirname'], 0777, true)) {
                                            $flag = true;
                                            $try = 1;
                                            while ($flag && $try <= 2):
                                                try {
                                                    $img = Image::make($image->img);
                                                    $img->resize(500, null, function ($constraint) {
                                                        $constraint->aspectRatio();
                                                    })->save($path);
                                                    //Image migrated successfully
                                                    $imageService->createImageWatermark(
                                                        $path,
                                                        $path,
                                                        public_path('images/watermarks/watermark.png')
                                                    );
                                                    AdvertImage::firstOrCreate(
                                                        ['path' => $userPath],
                                                        [
                                                            'advert_id' => $advert->id,
                                                            'path' => $userPath
                                                        ]);
                                                    $i++;
                                                    $flag = false;
                                                } catch (\Exception $e) {
                                                    //not throwing  error when exception occurs
                                                }
                                                $try++;
                                            endwhile;
                                        }

                                    }

                                }
                            }
                        }
                    }
                }
                if ($show_advert > 3000) {
                    $adverts = Advert::where('user_id', 6)->where('hide_advert', 1)->get();
                    foreach ($adverts as $advert) {
                        AdvertService::deleteAdvert($advert);
                    }
                } else {
                    echo 'Перезапустить, почистить базу';exit();
                }
                AdvertService::recountStylesAdverts();
                AdvertService::updateAdvertsOnCDN();
                $this->log->info('__FIN__');
                dd('FIN');
                break;
            case 'up_adverts':
                /**
                 * Рыба для обновлений пластинок на главной
                 */
                // тянем пластинки которые не обновлялись сутки
                $adverts = Advert::select()
                    ->where("status", 1)
                    ->where("up_time", "<", date("Y-m-d H:i:s", time() - (Advert::UP_TIME_HOUR * 3600)))
                    ->whereIn("user_id", [4,11, 6])
                    ->whereIn("style_id", /*[1, 19, 21, 20, 41, 46, 47, 48, 68]*/[1, 4, 17, 19, 20, 21, 22, 28, 30, 33, 41, 42])
                    ->inRandomOrder()
                    ->limit(12)
                    ->get();
           /*     foreach ($adverts as $advert) {
                    $advert->up_time = date("Y-m-d H:i:s", time() - (Advert::UP_TIME_OUR*3 * rand(1, 3600)));
                    $advert->save();
                }
                echo "FIN";exit();*/
            //    $date = [];
            //Обновляем их якобы в течение прошедшего часа
                foreach ($adverts as $advert) {
                    $advert->up_time = date("Y-m-d H:i:s", time() - (1 * rand(1, 3600)));
                   // $date[] = $advert->up_time;
                    $advert->save();
                }
            /*    foreach ($date as $item) {
                    echo Carbon::createFromFormat('Y-m-d H:i:s', $item) . "<br/>";
                }
                dd($date); */
                dd("FIN");
                break;
            case 'sync_cdn':
                /**
                 * Синхронизация изображений объявлений с cdn
                 */
                AdvertService::updateAdvertsOnCDN();
                dd("aba");
                break;
            case 'cron_translate':
                $trans = new GoogleTranslateService();
                $source = 'en';
                $target = 'ru';
               $artists = DiscogsArtist::select()->whereRaw('profile_translate IS NULL')->get();
               foreach ($artists as $artist) {
                   if ($artist->profile) {
                       $result = $trans->translate($source, $target, $artist->profile);
                       sleep(4);
                       if ($result) {
                           $artist->profile_translate = $result;
                           $artist->save();
                       } else {
                           dd($result);
                       }
                   }

               }
               echo 'abahaba';exit();
               break;
       /*     case 'peregon':
                $adverts = Advert::select('id', 'discogs_author_ids', 'check_discogs')->get();
                $sql = '';
                foreach ($adverts as $advert) {
                    $sql .= "UPDATE `adverts` SET discogs_author_ids='" . $advert->discogs_author_ids . "', check_discogs=1 WHERE id=" . $advert->id.';';
                }
                file_put_contents('progon.sql', $sql);exit();
                break;*/
            case 'cron_vk_post':
             /*   $lock = (int)date('i');
                if ($lock < 10) {
                    exit();
                }*/
                $styles = Style::select()->where('cron', 0)->where('count', '>', 0)->get();
                if (!count($styles)) {
                    DB::table('styles')->update(['cron' => 0]);
                    $styles = Style::select()->where('cron', 0)->where('count', '>', 0)->get();
                }
                foreach ($styles as $style) {
                    $adverts = Advert::select()
                        ->where('style_id', $style->id)->where('status', 1)
                        ->where('cron', 0)
                        ->orderBy('up_time', 'DESC')
                        ->limit(5)
                        ->get();
                    if (count($adverts)) {
                        $i = 1;
                        $message = "Пластинки в жанре " . mb_strtoupper($style->name) . "\r\n";
                        $images = [];
                        $dataMedia = [];
                        foreach ($adverts as $advert) {
                            $message .= $i . ") " . $advert->name ."\r\n";
                            if ($advert->author) {
                                $message .= "Автор: " . $advert->author . "\r\n";
                                $caption  = $advert->author . " | " . $advert->name;
                            } else {
                                $caption = $advert->name;
                            }
                            $price = '';
                            switch ($advert->deal) {
                                case "sale":
                                    $price = "Цена: " . str_replace('.00', '', $advert->price) . ' р.';
                                    break;
                                case "free":
                                    $price = 'Даром';
                                    break;
                                case "exchange":
                                    $price = 'Обмен';
                                    break;
                            }
                            $price .= "\r\n";
                            if ($advert->images) {
                                foreach ($advert->images as $image) {
                                    $images[] = thumb_file(storage_path('app/public') . $image->path);
                                    array_push($dataMedia, [
                                        'type'=> 'photo',
                                        'caption' => $caption,
                                        'media' =>  cdn_url(env('APP_URL') . $image->path, $image)
                                    ]);
                                    break;
                                }
                            }
                            $message .= $price ." ЗАБРАТЬ ПЛАСТИНКУ - " . route('vinyls.details', $advert->url) ."\r\n";
                            $i++;
                            $advert->cron = 1;
                            $advert->timestamps = false;
                            $advert->save();
                        }
                        if ($request->get('owner') && $request->get('album')) {
                            $vk = new VkService($request->get('owner'), $request->get('album'));
                        } else {
                            $vk = new VkService();
                        }
                        $result = $vk->addPhotos($images);
                        if (!$result['error']) {
                            $photos = $vk->savePhotos($result['responseBody']);
                            if ($photos) {
                                $vk->addPost($message, $photos);
                            } else {
                                $details = [
                                    'subject' => 'VK CRON ERROR',
                                    'message' => 'Изображения не сохранены'
                                ];

                                Mail::to(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\ErrorReporting($details));
                            }

                        } else {
                            $details = [
                                'subject' => 'VK CRON ERROR',
                                'message' => $result['error']
                            ];

                            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\ErrorReporting($details));
                        }
                        $style->cron = 1;
                        $style->save();
                        send_telegram('sendMediaGroup', [
                            'chat_id' => env('TELEGRAM_GROUP'),
                            'media' => json_encode($dataMedia)
                        ]);
                        send_telegram('sendMessage', [
                            'chat_id' => env('TELEGRAM_GROUP'),
                            'text' => $message
                        ]);
                        DB::table('styles')->update(['cron' => 0]);
                        echo "ADD POST FOR STYLE: " . $style->name ." SUCCESS";exit();
                    } else {
                        $style->cron = 1;
                        $style->save();
                        continue;
                    }
                }
                dd("FIN");
                break;
            case 'kufar':
                $pageUrl = 'https://www.kufar.by/l?query=%D0%BF%D0%BB%D0%B0%D1%81%D1%82%D0%B8%D0%BD%D0%BA%D0%B8&rgn=all&utm_queryOrigin=Manually_typed';
            //   $pageUrl = 'kufar.html';
                @$content = file_get_contents($pageUrl);
                if ($content) {
                    $doc1 = new DOMDocument();
                    @$doc1->loadHTML($content);
                    $xpath1 = new DOMXPath($doc1);
                    $productLists = $xpath1->query("//section");
                    if ($productLists->length) {
                        foreach ($productLists as $product) {
                            $title = $xpath1->query('a/div[2]/h3', $product)->item(0)->nodeValue;
                            if (!str_contains(mb_strtolower($title), 'пластинк')) {
                                continue;
                            }
                            $link = $xpath1->query('a', $product)->item(0)->getAttribute('href');
                            if ($link) {
                                if (preg_match("#/item/(\d+?)(\?|$)#is", $link, $pockets) && isset($pockets[1])) {

                                    $advertId = $pockets[1];
                                    $imageName = '';
                                    $imgSrc = '';
                                    if ($xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)->length) {
                                        $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                            ->item(0)->getAttribute('data-src');
                                        $parseUrl = parse_url($imgSrc);
                                        if (isset($parseUrl['path']) && !empty($parseUrl['path'])) {
                                            $imageName =  basename($parseUrl['path']);
                                        }
                                    }
                                    if ($imgSrc) {
                                        $image = '<a href="' . $imgSrc . '"> </a>';
                                    } else {
                                        $image = 'Изображение не задано';
                                    }
                                    $price =  $xpath1->query('a/div[2]/div/div/p/span[1]', $product)->item(0)->nodeValue;
                                    $priceText = 'Цена: ' . $price;
                                    $checkDoubleAdvert = Kufar::select()
                                        ->where('ad_id', $advertId)->first();
                                    // если объявление уже постилось в телегу и главное изображение не изменилось ничего не шлем
                                    if ($checkDoubleAdvert && $checkDoubleAdvert->main_image == $imageName
                                    && $checkDoubleAdvert->price == $price) {
                                        continue;
                                    } else {
                                        //если объявление уже постилось и сменили изображение, добавляем признак в пост
                                        $updateAdvert = false;
                                        if ($checkDoubleAdvert && $checkDoubleAdvert->main_image != $imageName) {
                                            $updateAdvert = true;
                                            $title .= ' (Изменилось главное изображение)';
                                        }
                                        if ($checkDoubleAdvert && $checkDoubleAdvert->price != $price) {
                                            $updateAdvert = true;
                                            $priceText .= ' (Было -   ' . $checkDoubleAdvert->price . ')';
                                        }
                                        $data = [
                                           'chat_id' => 910747903,
                                           'text' => $title . "\r\n\r\n" .
                                               $priceText . "\r\n" .
                                               $image
                                               . "\r\n" .
                                               '<a href="' . $link . '">Просмотр</a>',
                                           'parse_mode' => 'HTML',
                                           'disable_web_page_preview' => false
                                        ];
                                        $token = "5588441142:AAEGjw1O13jXR9n--dwbMY4gVXCvFHnRskg"; //bonkuf_bot BonKuf
                                        $ch = curl_init();
                                        curl_setopt_array(
                                           $ch,
                                           array(
                                               CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendMessage',
                                               CURLOPT_POST => TRUE,
                                               CURLOPT_RETURNTRANSFER => TRUE,
                                               CURLOPT_TIMEOUT => 10,
                                               CURLOPT_POSTFIELDS => $data,
                                           )
                                        );
                                        $res = json_decode(curl_exec($ch));
                                        if ($res->ok) {
                                            $data = [
                                                'title' => $title,
                                                'ad_id' => $advertId,
                                                'main_image' => $imageName,
                                                'price' => $price
                                            ];
                                            if ($updateAdvert) {
                                                $checkDoubleAdvert->update($data);
                                            } else {
                                                Db::table('kufar')->insert($data);
                                            }
                                        } else {
                                            echo 'Ne OK';exit();
                                        }
                                    }
                                } else {
                                    echo $link . "<br/>";continue;
                                    echo "error pockets for link";exit();
                                }
                            } else {
                                echo 'error parse link';exit();
                            }
                        }
                       echo "FIN";exit();
                    } else {
                        echo 'dont parse sections';exit();
                    }
                } else {
                    echo 'dont get content for page' . $pageUrl;exit();
                }
                break;
            case 'phone':
                $pageUrl = 'https://www.kufar.by/l/mobilnye-telefony?elementType=categories';
                //   $pageUrl = 'kufar.html';
                @$content = file_get_contents($pageUrl);
                if ($content) {
                    $priceMin = 0;
                    $priceMax = 300;
                    $doc1 = new DOMDocument();
                    @$doc1->loadHTML($content);
                    $xpath1 = new DOMXPath($doc1);
                    $productLists = $xpath1->query("//section");
                    if ($productLists->length) {
                        foreach ($productLists as $product) {
                            $title = $xpath1->query('a/div[2]/h3', $product)->item(0)->nodeValue;
                         /*   if (!str_contains(mb_strtolower($title), 'пластинк')) {
                                continue;
                            }*/
                            $link = $xpath1->query('a', $product)->item(0)->getAttribute('href');
                            if ($link) {
                                if (preg_match("#/item/(\d+?)(\?|$)#is", $link, $pockets) && isset($pockets[1])) {
                                    $advertId = $pockets[1];
                                    $imageName = '';
                                    $imgSrc = '';
                                    if ($xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)->length) {
                                        $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                            ->item(0)->getAttribute('data-src');
                                        $parseUrl = parse_url($imgSrc);
                                        if (isset($parseUrl['path']) && !empty($parseUrl['path'])) {
                                            $imageName =  basename($parseUrl['path']);
                                        }
                                    }
                                    if ($imgSrc) {
                                        $image = '<a href="' . $imgSrc . '"> </a>';
                                    } else {
                                        $image = 'Изображение не задано';
                                    }
                                    $price =  $xpath1->query('a/div[2]/div/div/p/span[1]', $product)->item(0)->nodeValue;
                                    if (str_contains($price, 'р.')) {
                                        $price = (int)trim(str_replace([' ', 'р.'], '', $price));
                                        if ($price && ($price >= $priceMin && $price <= $priceMax)) {
                                            $priceText = 'Цена: ' . $price;
                                            $checkDoubleAdvert = Phone::select()
                                                ->where('ad_id', $advertId)->first();
                                            // если объявление уже постилось в телегу и главное изображение не изменилось ничего не шлем
                                            if ($checkDoubleAdvert && $checkDoubleAdvert->main_image == $imageName
                                                && $checkDoubleAdvert->price == $price) {
                                                continue;
                                            } else {
                                                //если объявление уже постилось и сменили изображение, добавляем признак в пост
                                                $updateAdvert = false;
                                                if ($checkDoubleAdvert && $checkDoubleAdvert->main_image != $imageName) {
                                                    $updateAdvert = true;
                                                    $title .= ' (Изменилось главное изображение)';
                                                }
                                                if ($checkDoubleAdvert && $checkDoubleAdvert->price != $price) {
                                                    $updateAdvert = true;
                                                    $priceText .= ' (Было -   ' . $checkDoubleAdvert->price . ')';
                                                }
                                                $data = [
                                                    'chat_id' => 910747903,
                                                    'text' => $title . "\r\n\r\n" .
                                                        $priceText . "\r\n" .
                                                        $image
                                                        . "\r\n" .
                                                        '<a href="' . $link . '">Просмотр</a>',
                                                    'parse_mode' => 'HTML',
                                                    'disable_web_page_preview' => false
                                                ];
                                                $token = "6842208454:AAEXy-T-s7tgtwkfuDtJ9zcStANufziGNyY"; //bonkuf_bot BonKuf
                                                $ch = curl_init();
                                                curl_setopt_array(
                                                    $ch,
                                                    array(
                                                        CURLOPT_URL => 'https://api.telegram.org/bot' . $token . '/sendMessage',
                                                        CURLOPT_POST => TRUE,
                                                        CURLOPT_RETURNTRANSFER => TRUE,
                                                        CURLOPT_TIMEOUT => 10,
                                                        CURLOPT_POSTFIELDS => $data,
                                                    )
                                                );
                                                $res = json_decode(curl_exec($ch));
                                                if ($res->ok) {
                                                    $data = [
                                                        'title' => $title,
                                                        'ad_id' => $advertId,
                                                        'main_image' => $imageName,
                                                        'price' => $price
                                                    ];
                                                    if ($updateAdvert) {
                                                        $checkDoubleAdvert->update($data);
                                                    } else {
                                                        Db::table('kufar')->insert($data);
                                                    }
                                                } else {
                                                    echo 'Ne OK';exit();
                                                }
                                            }
                                        } else {
                                            continue;
                                        }
                                    } else {
                                        continue;
                                    }
                                } else {
                                    echo $link . "<br/>";continue;
                                    echo "error pockets for link";exit();
                                }
                            } else {
                                echo 'error parse link';exit();
                            }
                        }
                        echo "FIN";exit();
                    } else {
                        echo 'dont parse sections';exit();
                    }
                } else {
                    echo 'dont get content for page' . $pageUrl;exit();
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

