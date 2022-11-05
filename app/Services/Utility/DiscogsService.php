<?php
namespace App\Services\Utility;

use App\Models\DiscogsArtist;
use App\Models\DiscogsMaster;
use App\Models\DiscogsReleases;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Xyrotech\Orin;

class DiscogsService {

    private $advert;
    private $cdnService;
    private $imageService;
    private static $discogApi = null;
    private $searchRelease;
    private $whatReplace = [
        'сборка', 'сборный', 'сборник', 'various', 'разное', 'разные исполнители', 'various(сборник)',
        'сборник(various)', 'сборник (various)', 'various (сборник)'];
    private $toReplace = 'Various';
    private $various = false; // признак что исполнитель системное значение Various т.е Сборник
    private $artistIds;
    private $query;
    const   DISCOGS_SYSTEM_ID = 194; // id для Various артистов

    public function __construct($advert)
    {
        $this->setAdvert($advert);
        $this->cdnService = new CDNService();
        $this->imageService = new ImageService();
    }

    public static function getDiscogApi()
    {
        if (self::$discogApi) {
            return self::$discogApi;
        } else {
            return new Orin(Config::get('discogs'));
        }
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
            if ((int)$searchRelease->master_id === 0) {
                return false;
            }
            return $this->searchMasterRelease($searchRelease->master_id);
        }
        return false;
    }

    public function addMasterReleaseImagesInCDN($images, $master)
    {
        if (env('CDN_ENABLE')) {
            self::addImagesInCDN($images, $master);
        }
    }

    public static function addImagesInCDN($images, $item, $type = 'master')
    {
        if ($item->cdn_count_images) {
            // уже были загружены раньше
            return true;
        }
        $cdnService = new CDNService();
        $imageService = new ImageService();
        $i = 1;
        if ($type == 'release') {
            $partCdnPath = $item->artist_id . '/' . $item->id;
        } else {
            $partCdnPath = $item->id;
        }
        foreach ($images as $image) {
            $cdnPath =
                '/discogs/' . $type . '/' . $partCdnPath .
                '/' . $type . $i . '.' . pathinfo($image['uri'], PATHINFO_EXTENSION);
            $content = file_get_contents($image['uri']);
            if (!$content ||
                !make_directory(storage_path('app/public/discogs/tmp/' . $type . '/'
                    . $item->id), 0777, true)) {
                $item->no_images = 1;
                $item->save();
                break;
            } else {
                $fileTmpPath = storage_path('app/public/discogs/tmp/'. $type)
                    . '/' . $item->id. '/' . $type . $i . '.'
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
                    $item->no_images = 2; //если не все имаги загрузились
                    break;
                }
                $i++;
            }

        }
        $item->cdn_count_images = --$i;
        $item->save();
    }


    public function addArtistImagesInCDN($images, $artists)
    {
        if (env('CDN_ENABLE')) {
            foreach ($artists as $key => $artist) {
                if ($images[$key]) {
                    self::addImagesInCDN($images[$key], $artist, 'artist');
                }
            }
        }
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
                $artist = self::getDiscogApi()->artist($artistId);
                if ($artist->status_code !== 200) {
                    $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        "ArtistId: " . $artistId . "\r\n" . print_r($this->getAdvert(), true));
                } else {
                    $data = [
                        "name" => null,
                        "artist_id" => null,
                        "images" => null,
                        "realname" => null,
                        "profile" => null,
                   /*     "urls" => false, */
                        "namevariations" => null
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
                        $data['images'] = $images['images'];
                    } else {
                        $data['images'] = false;
                    }
                    if ($artist->name) {
                        $data['name'] = $artist->name;
                    }
                    if (isset($artist->realname)) {
                        $data['realname'] = $artist->realname;
                    } else {
                        $data['realname'] = false;
                    }
                    if ($artist->profile) {
                        $parts = explode("\r\n", $artist->profile);
                        if ($parts) {
                            $data['profile'] = $parts[0];
                        } else {
                            $data['profile'] = $artist->profile;
                        }
                    } else {
                        $data['profile'] = false;
                    }
                /*    if ($artist->urls) {
                        $data['urls'] = json_encode($artist->urls);
                    } */
                    if (isset($artist->namevariations)) {
                        $namevariations = json_encode($artist->namevariations, JSON_UNESCAPED_UNICODE);
                        if (mb_strlen($namevariations) > 1000) {
                            $data['namevariations'] = false;
                        } else {
                            $data['namevariations'] = $namevariations;
                        }
                    } else {
                        $data['namevariations'] = false;
                    }
                    // проверка на целосность данных
                    $listFalseValues = [];
                    $flagError = false;
                    foreach ($data as $d_key => $value) {
                        if (is_null($value)) {
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
        $this->setQuery($query);
        $searchRelease = self::getDiscogApi()->search($query, $params);
        if ($searchRelease->status_code != 200) {
            $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                print_r($searchRelease, true) . "\r\n" . print_r($advert, true));
        } else {
            //если ничего не найдено и был указан год, убираем год из параметров и повторяем запрос к api discogs
            if (!$searchRelease->results && isset($params['year'])) {
                unset($params['year']);
                $searchRelease = self::getDiscogApi()->search($query, $params);
                if ($searchRelease->status_code != 200) {
                    $this->errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        print_r($searchRelease, true) . "\r\n" . print_r($advert, true));
                } elseif ($searchRelease->results && isset($searchRelease->results[0])) {
                    $this->setSearchRelease($searchRelease->results[0]);
                    return $this->getSearchRelease();
                } else {
                    return [];
                    $this->errorReport("Ошибка на строке " . __LINE__ . " api discogs search return result empty");
                }
            } elseif ($searchRelease->results && isset($searchRelease->results[0])) {
                $this->setSearchRelease($searchRelease->results[0]);
                return $this->getSearchRelease();
            }
        }
        return false;
    }


    public static function updateArtistReleases()
    {
        $artists = DiscogsArtist::select()->where('releases', 0)->where('discogs_artist_id', '!=', 194)->get();
        foreach ($artists as $artist) {
            $artistFromApi = self::getDiscogApi()->artist($artist->discogs_artist_id);
            if ($artistFromApi->status_code != 200) {
                echo "Код не равен 200 при получении данных atrist Id:" . $artist->id;exit();
            } else {
                $artistName = $artistFromApi->name;
            }
            $result = self::getDiscogApi()->search("", ['per_page' => 500, 'format' => 'Vinyl',
                'artist'=> $artistName, 'type' => 'release']);
            if ($result->status_code != 200) {
                echo "Код не равен 200 при получении данных releases atrist " .
                    $artist->id . " : " . $artistName;exit();
            } else {
                $data = [
                    'artist_id' => $artist->id,
                    'title' => null,
                    'country' => null,
                    'year' => null,
                    'label' => null,
                    'genre' => null,
                    'style' => null,

                ];
                foreach ($result->results as $release) {
                    if (isset($release->title) && !empty($release->title)) {
                        $data['title'] = $release->title;
                        if (mb_strlen($data['title']) > 500) {
                            $data['title'] = mb_substr($data['title'], 0, 490) . '...';
                        }
                    } else {
                        continue;
                    }
                    if (isset($release->country) && !empty($release->country)) {
                        $data['country'] = $release->country;
                    } else {
                        $data['country'] = 0;
                    }
                    if (isset($release->year) && !empty($release->year)) {
                        $data['year'] = $release->year;
                    } else {
                        $data['year'] = 0;
                    }
                    if (isset($release->label) && !empty($release->label)) {
                        $data['label'] = json_encode(array_slice($release->label, 0, 10), JSON_UNESCAPED_UNICODE);
                    } else {
                        $data['label'] = 0;
                    }
                    if (isset($release->genre) && !empty($release->genre)) {
                        $data['genre'] = json_encode($release->genre, JSON_UNESCAPED_UNICODE);
                    } else {
                        $data['genre'] = 0;
                    }
                    if (isset($release->style) && !empty($release->style)) {
                        $data['style'] = json_encode($release->style, JSON_UNESCAPED_UNICODE);
                    } else {
                        $data['style'] = 0;
                    }

                    // проверка на целосность данных
                    $listFalseValues = [];
                    $flagError = false;
                    foreach ($data as $d_key => $value) {
                        if (is_null($value)) {
                            $listFalseValues[] = $d_key;
                            $flagError = true;
                        }
                    }
                    if ($flagError) {
                        echo "In update releases artistId: " . $artist->id . " not found params: " .
                            implode(", ", $listFalseValues);echo "<br/>";
                            dd($data);
                        exit();
                    }
                    if(isset($release->cover_image) && !empty($release->cover_image)) {
                        $data['cover_image'] = $release->cover_image;
                    }
                    DiscogsReleases::firstOrCreate($data);

                }
            }
            $artist->releases = 1;
            $artist->save();

        }

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
        $searchMasterRelease = self::getDiscogApi()->master_release($masterId, $params);
        if ($searchMasterRelease->status_code == 200) {
            //проверяем наличие всех необходимых параметров для записи в базу
            $data = [
                "master_id" => null,
                "images" => null,
                "genres" => null,
                "styles" => null,
                "year" => null,
                "tracklist" => null,
                "artists" => null,
                "title" => null,
                'ad_search' => $this->getQuery()

            ];
            if ((int)$searchMasterRelease->id) {
                $data["master_id"] =  (int)$searchMasterRelease->id;
            }
            if (isset($searchMasterRelease->images) && !empty($searchMasterRelease->images)) {
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
                $data['images'] = $images['images'];
            } else {
                $data['images'] = false;
            }
            if (isset($searchMasterRelease->genres)) {
                $data['genres'] = json_encode($searchMasterRelease->genres, JSON_UNESCAPED_UNICODE);
            } else {
                $data['genres'] = false;
            }
            if (isset($searchMasterRelease->styles)) {
                $data['styles'] = json_encode($searchMasterRelease->styles, JSON_UNESCAPED_UNICODE);
            } else {
                $data['styles'] = false;
            }
            if ($searchMasterRelease->year) {
                $data['year'] = (int)$searchMasterRelease->year;
            } else {
                $data['year'] = false;
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
            } else {
                $data['tracklist'] = false;
            }
            if ($searchMasterRelease->artists && is_array($searchMasterRelease->artists)) {
                $artistsIds = $this->setArtistsIds($searchMasterRelease->artists);
                if ($artistsIds == 'Various not match') {
                    return [];
                }
                $data['artists'] = implode(',', $this->getArtistsIds());
            }
            if ($searchMasterRelease->title) {
                $data['title'] = $searchMasterRelease->title;
            }
            // проверка на целосность данных
            $listFalseValues = [];
            $flagError = false;
            foreach ($data as $key => $value) {
                if (is_null($value)) {
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

    public function getQuery()
    {
        return $this->query;
    }
    public function setQuery($query)
    {
        $this->query = $query;
    }
    public function setArtistsIds($artists)
    {
        if (is_integer($artists)) {
            return $this->artistIds = [$artists];
        }
        $artistIds = [];
        foreach ($artists as $artist) {
            if ($this->various && $artist->name != $this->toReplace) {
                return 'Various not match';
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
    /**
     * Replace in field discogs_artists.profile link type [a=*****], [b] & etc.
     * In Base - SELECT id, profile FROM `discogs_artists` WHERE profile LIKE '%[a=%';
     * Examples 1:
     * $toHtml = ['b', 'i'];
     * DiscogsService::replaceProfileTags($toHtml);
     * Examples 2:
     * $tagList = ["a=","a", "l=", "ʃ", "url"];
     * $toReplace = ["", "artistId", "", "", ""];
     * DiscogsService::replaceProfileTags($tagList, $toReplace);
     */
    public static function replaceProfileTags($tagList, $toReplace = null)
    {
        if ($toReplace && count($tagList) == count($toReplace)) {
            foreach ($tagList as $tag) {
                if ($tag == "a") {
                    $profiles = DiscogsArtist::select()->where("profile", "LIKE", '%[' . $tag . '%')->get();
                    foreach ($profiles as $profile) {
                        //@todo написано но не проверено
                        if (preg_match_all("#\[' . $tag . '(\d+?)]#is", $profile->profile, $pockets)) {
                            foreach ($pockets as $pocket) {
                                for ($i=0; $i<count($pocket); $i++) {
                                    $artistId = str_replace(["[" . $tag, "]"], "", $pocket[$i]);
                                    $artist = self::getDiscogApi()->artist($artistId);
                                    if ($artist->name) {
                                        $profile->profile = str_replace($pocket[$i], $artist->name, $profile->profile);
                                    } else {
                                        echo 'Artist not found for ID ' . $profile->id;exit();
                                    }
                                    sleep(1);
                                }
                            }
                            $profile->save();
                        } else {
                            echo "Nichego";exit();
                        }
                    }
                } else {
                    $profiles = DiscogsArtist::select()->where("profile", "LIKE", '%[' . $tag . '%')->get();
                    foreach ($profiles as $profile) {
                        if ($tag == 'url') {
                            $profile->profile = preg_replace(['#\[url=(.+?)\]#is', '#\[/url\]#is'], '', $profile->profile);
                        } else {
                            $profile->profile = preg_replace('#\[' . $tag . '(.+?)]#is','$1' ,$profile->profile);
                        }
                        $profile->save();
                    }
                }

            }
            //замена на html
        } else if (is_array($tagList)) {
            foreach ($tagList as $whatReplace) {
                $profiles = DiscogsArtist::select()->where("profile", "LIKE", '%[' . $whatReplace . ']%')->get();
                foreach ($profiles as $profile) {
                    $profile->profile = str_replace(
                        ["[" . $whatReplace . "]", "[/" . $whatReplace . "]"],
                        ["<" . $whatReplace . ">", "</". $whatReplace . ">"],
                        $profile->profile
                    );
                    $profile->save();
                }
            }
        } else {
            return false;
        }

        return true;
    }

    private function errorReport($message)
    {
        die($message);
    }

    public static function getArtistsLink($discogsArtistIds)
    {
        if ($discogsArtistIds) {
            $systemId = DiscogsService::DISCOGS_SYSTEM_ID;
            if ($discogsArtistIds && $discogsArtistIds != $systemId) {
                $authors_ids = str_replace([$systemId, ',' . $systemId, $systemId . ','], '', $discogsArtistIds);
                $authorLists = explode(',', $authors_ids);
                $links = [];
                foreach ($authorLists as $artistId) {
                    $discogsAuthor = DiscogsArtist::select('name')->where('discogs_artist_id', $artistId)->first();
                    if ($discogsAuthor) {
                        $links[] =  '<a href="' . route('artist', $artistId) . '">'
                            . $discogsAuthor->name . '</a>';
                    }
                }
                if ($links)  {
                    return implode(', ', $links);
                }
            }
        }

        return false;
    }

    public static function getArtistReleases($artist)
    {
        if ($artist->releases) {
            $discogsAuthorReleases = DiscogsReleases::select()
                ->where('artist_id', $artist->id)
                ->orderBy('year', 'DESC')->get();
            if ($discogsAuthorReleases) {
                return $discogsAuthorReleases;
            }
        }
        return false;
    }

    public static function uploadReleasesFileOnDisc()
    {
        $artists = DiscogsArtist::select('id','releases', 'name', 'discogs_artist_id', 'releases_file')
            ->where('releases', 1)->where('releases_file', 0)->get();
        foreach ($artists as $artist) {
            $releases = DiscogsReleases::select()->where('artist_id', $artist->id)->orderBy('year')->get();
            $data = json_encode($releases);
            $path = storage_path('app/public/discogs/releases/') . $artist->id;
            if (make_directory($path, 0777, true)) {
                if (file_put_contents($path . '/' . $artist->id . '.data', $data)) {
                    $artist->releases_file = 1;
                    $artist->save();
                    foreach ($releases as $release) {
                        $release->delete();
                    }

                }
            } else {
                echo "Не удалось создать путь для релиза artistId " . $artist->id; exit();
            }
        }
    }
}
?>

