<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\AyBy;
use App\Models\CanvasSize;
use App\Models\Catalog;
use App\Models\Color;
use App\Models\DiscogsArtist;
use App\Models\Door;
use App\Models\Edition;
use App\Models\Kufar;
use App\Models\Locker;
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
    private $ayIds;
    private $ayNewAdverts = [];
    const LIMIT_NEW_SKUS = 100;
    private $limitAyPages = 1000; // чтобы чистить удаленные объявления нужно устанавливать лимит ровно 1000
    const MIN_PRODUCTS = 3000;

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
            case 'parser_step2':
                $this->log = Log::channel('parser-vinil-sd-by');
                $locker = Locker::where('type', 'parser-sd2')->first();
                if (!$locker->status_lock) {
                    $locker->status_lock = 1;
                    $locker->save();
                    AdvertService::updateAdvertsOnCDN();
                    AdvertService::createAdThumb();
                    $locker->status_lock = 0;
                    $locker->save();
                    $this->log->info('__FIN__ 2');
                } else {
                    echo 'lock';exit();
                }
                dd('FIN');
                break;
            case 'parser-ay':
                if ($request->limit) {
                    $this->limitAyPages = $request->limit;
                }
                $this->log = Log::channel('parser-ay');
              //  $locker = Locker::where('type', 'parser-ay')->first();
               // if (!$locker->status_lock) {
              //      $locker->status_lock = 1;
              //      $locker->save();
                    $categories = AyBy::TYPES;
                    foreach ($categories as $cat) {
                        $typeId = array_search($cat, AyBy::TYPES);
                        $this->ayIds = [];
                        $this->log->info('Обновление товаров в категории ' . $cat);
                        $this->parseAyCategory($cat);
                        //если собираем обновления по всем страницам категории, то чистим удаенные объявления
                        if ($this->limitAyPages == 1000) {
                            $count = AyBy::whereNotIn('ay_id',$this->ayIds)->where('type', $typeId)->delete();
                            $this->log->info('Очистка категории ' . $cat . '.Удалено - ' . $count);
                        }
                    }
                //    $locker->status_lock = 0;
                  //  $locker->save();
                    if ($this->ayNewAdverts) {
                        foreach ($this->ayNewAdverts as $text) {
                            $data = [
                                'chat_id' => 910747903,
                                'text' => $text,
                                'parse_mode' => 'HTML',
                                'disable_web_page_preview' => false
                            ];
                            $token = "6963367076:AAECDLZK0wpPVdvdq-8c6hCg-byBw6jnulI"; //
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
                        }
                    }
                    if ($request->auto_ref) {
                        echo 'FIN';exit();
                    }
                    return redirect()->route('main.ay-list');
               /* } else {
                    echo 'lock';exit();
                }*/

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
            case 'parser_step1':
                $this->log = Log::channel('parser-vinil-sd-by');
                /**
                 * Парсер с сайта vinil-sd.by
                 */
                $locker = Locker::where('type', 'parser-sd')->first();
                if (!$locker->status_lock) {
                    $locker->status_lock = 1;
                    $locker->save();
                    $show_advert = 0;
                    $products = $this->getParserVinylProudcts();
                    if (count($products) > self::MIN_PRODUCTS) {
                        if ($this->checkParserVinylProducts($products)) {
                            Advert::where('user_id', 6)->update(['hide_advert' => 1]);
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
                                    if (empty($product->price)) {
                                        dd($product);
                                        $advert->price = 0;
                                    } else {
                                        $advert->price = $product->price;
                                    }
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
                                        //'url' => translate_url($product->title) . '-' . $product->uid ,
                                        'url' => translate_url($product->title . '-' . $product->uid . '-' . $product->sku) ,
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
                                    $this->log->info('NEW PRODUCT SKU ' . $product->sku);
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
                            if ($show_advert > self::MIN_PRODUCTS) {
                                $adverts = Advert::where('user_id', 6)->where('hide_advert', 1)->get();
                                foreach ($adverts as $advert) {
                                    AdvertService::deleteAdvert($advert);
                                }
                            } else {
                                echo 'Перезапустить, почистить базу';exit();
                            }
                            AdvertService::recountStylesAdverts();
                        } else {
                            echo 'Error Skus';exit();
                        }
                    }
                    $locker->status_lock = 0;
                    $locker->save();
                    $this->log->info('__FIN__');
                } else {
                    echo 'lock';exit();
                }
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
              //  AdvertService::updateAdvertsOnCDN();
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
                        ->where('deal', '!=', 'news')
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
                                case "news":
                                    $price = 'Новость';
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
                                    // заглушка на баг
                                    if ($advertId == 215148112) continue;
                                    $imageName = '';
                                    $imgSrc = '';
                                    if ($xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)->length) {
                                       /* $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                            ->item(0)->getAttribute('data-src');*/
                                        $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                            ->item(0)->getAttribute('src');
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
            case 'test_ay_bot':
                $text = "1) Title (33) - \r\n\r\n" .
                    "2) Title 2 (333) -\r\n
                        <a href='https://rms.kufar.by/v1/line_thumbs/adim1/ef809232-78f2-416a-bb4a-c7499be9dd61.jpg'> fdds</a>\r\n
                        <a href='https://rms.kufar.by/v1/line_thumbs/adim1/ef809232-78f2-416a-bb4a-c7499be9dd61.jpg'> fdds</a>\r\n";
                $data = [
                    'chat_id' => 910747903,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => false
                ];
                $token = "6963367076:AAECDLZK0wpPVdvdq-8c6hCg-byBw6jnulI"; //
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
                dd($res);
                break;
            case 'phone':
                $pageUrl = 'https://www.kufar.by/l/mobilnye-telefony?query=iphone';
                //   $pageUrl = 'kufar.html';
                @$content = file_get_contents($pageUrl);
                if ($content) {
                    $priceMin = 0;
                    $priceMax = 700;
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
                                        /* $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                           ->item(0)->getAttribute('data-src');*/
                                        $imgSrc =  $xpath1->query('a/div[1]/div/div[1]/div/div/div/img[1]', $product)
                                            ->item(0)->getAttribute('src');
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
                                                        Db::table('phone')->insert($data);
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

    private function getParserVinylProudcts()
    {
        $vinylProducts = [];
        $jCount = 13; //13
        for ($j=1; $j<=$jCount; $j++) {
            $slice = $j;
            $siteUrl = 'https://store.tildacdn.com/api/getproductslist/?storepartuid=495183118261&recid=375529451&c=1665522162862&getparts=true&getoptions=true&slice=' . $slice . '&size=500';

            $data = json_decode(file_get_contents($siteUrl));
            $products = $data->products;
            if (!count($products)) {
                break;
            }
            $this->log->info('__PROHOD__', ['j' => $j]);
            $vinylProducts = array_merge($vinylProducts, $products);
            sleep(1);
        }
        return $vinylProducts;
    }

    private function checkParserVinylProducts($products)
    {

        $skus = [];
        foreach ($products as $product) {
            if (isset($product->sku) && !empty($product->sku)) {
                $skus[] = $product->sku;
            }
        }
        $countSkus = count($skus);
        $countAdverts = Advert::select()->whereIn('sku', $skus)->where('user_id', 6)->count();
        $diffSku = $countSkus - $countAdverts ;
        if ($diffSku > self::LIMIT_NEW_SKUS) {
            echo "Новых товаров больше " . self::LIMIT_NEW_SKUS .
            ": <br/>Всего skus: " . $countSkus .
            "<br/>Всего соответствий в базе:"  . $countAdverts .
            "<br/>В базе не найдено: " . $diffSku;
            exit();
        }
        return true;
    }

    private function parseAyCategory($type, $page = 1, $countPages = 0) {
        $this->log->info('Парсинг категории ' . $type . ', страница ' . $page);
        $pageUrl = 'http://films-music.ay.by/muzyka/plastinki/' . $type . '/?order=create&page=' . $page;
        @$content = file_get_contents($pageUrl);
        if ($content) {
            $doc1 = new DOMDocument();
            @$doc1->loadHTML($content);
            $xpath1 = new DOMXPath($doc1);
            //получаем количество страниц в пагинации
            if ($page === 1) {
                $pagination = $xpath1->query('//li[@class="g-pagination__list__li pg-link pg-last"]');
                if ($pagination->length) {
                    $countPages = $pagination->item(0)->getAttribute('data-value');
                } else {
                    echo 'dont get count pages for category ay category: ' . $type;exit();
                }
            }
            $productLists = $xpath1->query('//ul/li[@class = "viewer-type-grid__li " or contains(@class, "viewer-type-grid__li  item-type-card_hot")]');
            if ($productLists->length) {
                //Парсим страницы категории
                foreach ($productLists as $product) {
                    $ayId = $product->getAttribute('data-value');
                    $link = $xpath1->query('div[@class="viewer-type-grid__col"]//a', $product)?->item(0)?->getAttribute('href');
                    $title = $xpath1->query('div[@class="viewer-type-grid__col"]//p[@class="item-type-card__title"]', $product)?->item(0)?->nodeValue;
                    $priceHot = trim(str_replace(',', '.', $xpath1->query('div[@class="viewer-type-grid__col"]//span[@class="c-hot"]/strong', $product)?->item(0)?->nodeValue));
                    $priceAy = trim(str_replace(',', '.', $xpath1->query('div[@class="viewer-type-grid__col"]//p[@class="item-type-card__info"]/strong',$product)?->item(0)?->nodeValue));
                    $imgUrl = $xpath1->query('div[@class="viewer-type-grid__col"]//img', $product)?->item(0)?->getAttribute('src');
                    $author = $xpath1->query('div[@class="viewer-type-grid__col"]//p[@class="item-type-card__author"]', $product)?->item(0)?->nodeValue;
                    if ($ayId && $link && $title && ($priceHot || $priceAy) && $imgUrl) {
                        $auction = 0;
                        if (empty($priceHot)) {
                            $priceHot = 0;
                            $auction = 1;
                        }
                        if (empty($priceAy)) {
                            $priceAy = 0;
                        }
                        $this->ayIds[] = $ayId;
                        $typeId = array_search($type, AyBy::TYPES);

                        $new = 1;
                        $ayItemFind = AyBy::where('ay_id', $ayId)->first();
                        if ($ayItemFind) {
                            $updatedPrice = 0;
                            $priceAuctionOld = 0;
                            $priceHotOld = 0;
                           if ($priceAy != $ayItemFind->price_auction) {
                               $updatedPrice = 1;
                               $priceAuctionOld = $ayItemFind->price_auction;
                           }
                           if ($priceHot != $ayItemFind->price_hot) {
                               $updatedPrice = 1;
                               $priceHotOld = $ayItemFind->price_hot;
                           }
                           AyBy::where('id', $ayItemFind->id)->update([
                               'new' => 0,
                               'price_hot' => $priceHot,
                               'price_hot_old' => $priceHotOld,
                               'price_auction_old' => $priceAuctionOld,
                               'price_auction' => $priceAy,
                               'updated_price' => $updatedPrice,
                               'auction' => $auction
                           ]);

                        } else {
                            $this->log->info('Добавление товара (' . $ayId . ') в категории ' . $type);
                            $content = @file_get_contents($imgUrl);
                            $imgExt = '';
                            $downloadImg = 0;
                            if ($content) {
                                $imgExt =  pathinfo($imgUrl, PATHINFO_EXTENSION);
                                if (file_put_contents(storage_path('app/public/ay/' . $ayId . '.' . $imgExt),
                                    $content)) {
                                    $downloadImg = 1;
                                }
                            }
                            AyBy::create([
                                'ay_id' => $ayId,
                                'title' => $title,
                                'author' =>  $author,
                                'price_hot' => $priceHot,
                                'price_auction' => $priceAy,
                                'img_url' => $imgUrl,
                                'img_ext' => $imgExt,
                                'link' => $link,
                                'type' => $typeId,
                                'auction' => $auction,
                                'new' => $new,
                                'download_img' => $downloadImg,

                            ]);
                            $imgSrc = 'https://vinylflea.by/storage/ay/' . $ayId . '.' . $imgExt;
                            $image = '<a href="' . $imgSrc . '">'. "&nbsp;" . '</a>';
                            $this->ayNewAdverts[] = $title .
                                " (" . $priceHot . " | " . $priceAy . ")\r\n" . $image . "\r\n\r\n";
                        }

                    } else {
                        echo 'Empty params: ayId = ' . $ayId .
                            ', link = ' . $link . ', title = ' . $title . ', priceHot = ' . $priceHot .
                            ', priceAy = ' . $priceAy . ', imgUrl = '
                            . $imgUrl . ', author = ' . $author;exit();
                    }
                }
                $page++;
                if ($page > $this->limitAyPages) {
                    return true;
                }
                if ($page <= $countPages) {
                    $this->parseAyCategory($type, $page, $countPages);
                } else {
                    return true;
                }

            } else {
                echo 'dont parse li blocks for ay category: ' . $type;exit();
            }
        } else {
            echo 'dont get content for ay category: ' . $type;exit();
        }
    }
}

