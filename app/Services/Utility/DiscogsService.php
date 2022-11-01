<?php
namespace App\Services\Utility;

use App\Models\DiscogsMaster;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Xyrotech\Orin;

class DiscogsService {

    private $advert;
    private $cdnService;
    private $imageService;
    private $discogApi;
    private $searchRelease;
    private $whatReplace = [
        'сборка', 'сборный', 'сборник', 'various', 'разное', 'разные исполнители', 'various(сборник)',
        'сборник(various)', 'сборник (various)', 'various (сборник)'];
    private $toReplace = 'Various';
    private $various = false; // признак что исполнитель системное значение Various т.е Сборник
    private $artistIds;
    const   DISCOGS_SYSTEM_ID = 194; // id для Various артистов

    public function __construct($advert)
    {
        $this->setAdvert($advert);
        $this->cdnService = new CDNService();
        $this->imageService = new ImageService();
        $this->discogApi = new Orin(Config::get('discogs'));
    }

    public function setAdvert($advert)
    {
        $this->advert = $advert;
    }

    public function getAdvert()
    {
        return $this->advert;
    }

    public function getMasterReleaseData()
    {
        $advert = $this->getAdvert();
        //получаем релиз
        $searchRelease = $this->searchRelease($advert);
        if ($searchRelease) {
            return $this->searchMasterRelease($searchRelease->master_id);
        }
  /*
                            $master = DiscogsMaster::firstOrCreate(['ad_search' => $query], $data);
                            $advert->check_discogs = 1;
                            $advert->save();
                            //Заливаем файлы на cdn
                            if (env('CDN_ENABLE')) {
                                $i = 1;
                                foreach ($images as $image) {
                                    $cdnPath =
                                        '/discogs/master/' . $master->id .
                                        '/vinyl' . $i . '.' . pathinfo($image['uri'], PATHINFO_EXTENSION);
                                    $content = file_get_contents($image['uri']);
                                    if (!$content ||
                                        !make_directory(storage_path('app/public/discogs/tmp/'
                                            . $master->id),0777, true)) {
                                        $master->no_images = 1;
                                        break;
                                    } else {
                                        $fileTmpPath = storage_path('app/public/discogs/tmp')
                                            . '/' . $master->id. '/vinyl' . $i . '.'
                                            . pathinfo($image['uri'], PATHINFO_EXTENSION);
                                        file_put_contents($fileTmpPath, $content);
                                        $img = Image::make($fileTmpPath);
                                        $img->resize(500, null, function ($constraint) {
                                            $constraint->aspectRatio();
                                        })->save($fileTmpPath);
                                        $imageService->createImageWatermark(
                                            $fileTmpPath,
                                            $fileTmpPath,
                                            public_path('images/watermarks/watermark.png')
                                        );
                                        if ($cdnService->uploadFile($fileTmpPath,$cdnPath)['error']) {
                                            $master->no_images = 2; //если не все имаги загрузились
                                            break;
                                        }
                                        $i++;
                                    }

                                }
                                $master->cdn_count_images = --$i;
                                $master->save();
                            }
                            echo 'abahaba';exit();
                            */


    }

    public function getArtistsData()
    {
        try {
            $artistsIds = $this->getArtistsIds();
            $artistList = [];
            foreach ($artistsIds as $key => $artistId) {
                if ($artistId == self::DISCOGS_SYSTEM_ID) {
                    $artistList[$key]['artist_id'] = self::DISCOGS_SYSTEM_ID;
                    continue;
                }
                $artist = $this->discogApi->artist($artistId);
                if ($artist->status_code !== 200) {
                    $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        "ArtistId: " . $artistId . "\r\n" . print_r($this->getAdvert(), true));
                } else {
                    $data = [
                        "name" => false,
                        "artist_id" => false,
                        "images" => false,
                        "realname" => false,
                        "profile" => false,
                        "urls" => false,
                        "namevariations" => false
                    ];
                    if ((int)$artist->id) {
                        $data["artist_id"] =  (int)$artist->id;
                    }
                    if (!empty($artist->images)) {
                        $images = [];
                        $i=0;
                        foreach ($artist->images as $k => $image) {
                            if ($i>3) break;
                            $images['images'][$k]['type'] = $image->type;
                            $images['images'][$k]['uri'] = $image->uri;
                            $images['images'][$k]['resource_url'] = $image->resource_url;
                            $images['images'][$k]['uri150'] = $image->uri150;
                            $images['images'][$k]['width'] = $image->width;
                            $images['images'][$k]['height'] = $image->height;
                            $i++;
                        }
                        $data['images'] = json_encode($images['images']);
                    }
                    if ($artist->name) {
                        $data['name'] = $artist->name;
                    }
                    if ($artist->realname) {
                        $data['realname'] = $artist->realname;
                    }
                    if ($artist->profile) {
                        $data['profile'] = $artist->profile;
                    }
                    if ($artist->urls) {
                        $data['urls'] = json_encode($artist->urls);
                    }
                    if ($artist->namevariations) {
                        $data['namevariations'] = json_encode($artist->namevariations);
                    }
                    // проверка на целосность данных
                    $listFalseValues = [];
                    $flagError = false;
                    foreach ($data as $d_key => $value) {
                        if (!$value) {
                            $listFalseValues[] = $d_key;
                            $flagError = true;
                        }
                    }
                    if ($flagError) {
                        $this->errorReport("In artistId: " . $artistId . " not found params: " .
                            implode(", ", $listFalseValues));
                    } else {
                        $artistList[$key] = $data;
                    }
                }
            }
            return $artistList;
        } catch (ClientException $e) {
            $this->errorReport("Exception на строке " . __LINE__ . "\r\n Message: " . $e->getMessage());
        }
    }
    /**
     * Из рекламы берется три параметра для поиска author, name, year
     * Возвращает либо массив данных api discogs release либо false либо выводит ошибку по die и обрывает выполнение
     */
    public function searchRelease($advert)
    {
        $params = [
            "format" => "Vinyl",
            "type" => "release"
        ];
        $query = $advert->name;
        if ($advert->author) {
            $various = $this->maybeVarious($advert->author);
            if ($various) {
                $query = $various. ' - ' . $query;
            } else {
                $query = $advert->author . ' - ' . $query;
            }
        }
        if ($advert->year && $advert->year > 1900) {
            $params['year'] = $advert->year;
        }
        //ищем релиз по заданным году, автору и названию
        $searchRelease = $this->discogApi->search($query, $params);
        if ($searchRelease->status_code != 200) {
            $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                print_r($searchRelease, true) . "\r\n" . print_r($advert, true));
        } else {
            //если ничего не найдено и был указан год, убираем год из параметров и повторяем запрос к api discogs
            if (!$searchRelease->results && isset($params['year'])) {
                unset($params['year']);
                $searchRelease = $this->discogApi->search($query, $params);
                if ($searchRelease->status_code != 200) {
                    $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        print_r($searchRelease, true) . "\r\n" . print_r($advert, true));
                } elseif ($searchRelease->results && isset($searchRelease->results[0])) {
                    $this->setSearchRelease($searchRelease->results[0]);
                    return $this->getSearchRelease();
                } else {
                    $this->errorReport("Ошибка на строке " . __LINE__ . " api discogs search return result empty");
                }
            } elseif ($searchRelease->results && isset($searchRelease->results[0])) {
                $this->setSearchRelease($searchRelease->results[0]);
                return $this->getSearchRelease();
            }
        }
        return false;
    }

    public function setSearchRelease($searchRelease)
    {
        $this->searchRelease = $searchRelease;
    }
    public function getSearchRelease()
    {
        return $this->searchRelease;
    }

    /**
     * Мастер релиз тянется из api после успешного получения релиза если по какой-то пречине не удается получить
     *  мастера, возвращаемся к начальному релизу для вставки в базу
     */
    private function searchMasterRelease($masterId)
    {
        $params = ['format' => 'Vinyl'];
        //получаем id master и вытягиваем с него инфу
        $searchMasterRelease = $this->discogApi->master_release($masterId, $params);
        if ($searchMasterRelease->status_code == 200) {
            //проверяем наличие всех необходимых параметров для записи в базу
            $data = [
                "master_id" => false,
                "images" => false,
                "genres" => false,
                "styles" => false,
                "year" => false,
                "tracklist" => false,
                "artists" => false,
                "title" => false

            ];
            if ((int)$searchMasterRelease->id) {
                $data["master_id"] =  (int)$searchMasterRelease->id;
            }
            if (!empty($searchMasterRelease->images)) {
                $images = [];
                $i=0;
                foreach ($searchMasterRelease->images as $k => $image) {
                    if ($i>3) break;
                    $images['images'][$k]['type'] = $image->type;
                    $images['images'][$k]['uri'] = $image->uri;
                    $images['images'][$k]['resource_url'] = $image->resource_url;
                    $images['images'][$k]['uri150'] = $image->uri150;
                    $images['images'][$k]['width'] = $image->width;
                    $images['images'][$k]['height'] = $image->height;
                    $i++;
                }
                $data['images'] = json_encode($images['images']);
            }
            if ($searchMasterRelease->genres) {
                $data['genres'] = json_encode($searchMasterRelease->genres);
            }
            if ($searchMasterRelease->styles) {
                $data['styles'] = json_encode($searchMasterRelease->styles);
            }
            if ($searchMasterRelease->year) {
                $data['year'] = (int)$searchMasterRelease->year;
            }
            if ($searchMasterRelease->tracklist) {
                $tracklists = [];
                $tracklist = '';
                foreach ($searchMasterRelease->tracklist as $item) {
                    $tracklist = $item->position . ') ';
                    if (isset($item->artists)) {
                        $artists = [];
                        foreach ($item->artists as $artist) {
                            $artists[] = $artist->name;
                        }
                        $tracklist .= implode(', ', $artists) . ' - ' . $item->title;
                    } else {
                        $tracklist .= $item->title;
                    }
                    if ($item->duration) {
                        $tracklist .= ' [' . $item->duration. ']';
                    }
                    $tracklists[] = $tracklist;
                }
                if ($tracklists) {
                    $tracklist = implode("\r\n", $tracklists);
                }

                $data['tracklist'] = $tracklist;
            }
            if ($searchMasterRelease->artists && is_array($searchMasterRelease->artists)) {
                $this->setArtistsIds($searchMasterRelease->artists);
                $data['artists'] = json_encode($searchMasterRelease->artists);
            }
            if ($searchMasterRelease->title) {
                $data['title'] = $searchMasterRelease->title;
            }
            // проверка на целосность данных
            $listFalseValues = [];
            $flagError = false;
            foreach ($data as $key => $value) {
                if (!$value) {
                    $listFalseValues[] = $key;
                    $flagError = true;
                }
            }
            if ($flagError) {
                $this->errorReport("In masterId: " . $masterId . " not found params: " .
                    implode(", ", $listFalseValues));
            }
            return $data;
        } else {
            $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                print_r($searchMasterRelease, true) . "\r\n" . print_r($this->getAdvert(), true));
        }
    }

    private function maybeVarious($author)
    {
        if (in_array(mb_strtolower($author), $this->whatReplace)) {
            $this->various = true;
            return $this->toReplace;
        }
        return '';
    }

    public function isVarious()
    {
        return $this->various;
    }
    public function setArtistsIds($artists)
    {
        $artistIds = [];
        foreach ($artists as $artist) {
            if ($this->various && $artist->name != $this->toReplace) {
                $this->errorReport("Артист отмечен как various в поле adverts.author, но в мастере он "
                    . $artist->name);
            }
            $artistIds[] = $artist->id;
        }
        $this->artistIds = $artistIds;
    }

    public function getArtistsIds() {
        return $this->artistIds;
    }

    private function errorReport($message)
    {
        die($message);
    }
}
?>

