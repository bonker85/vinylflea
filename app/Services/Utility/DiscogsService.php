<?php
namespace App\Services\Utility;

use App\Models\DiscogsArtist;
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
    private static $whatReplace = [
        'сборка', 'сборный', 'сборник', 'various', 'разное', 'разные исполнители', 'various(сборник)',
        'сборник(various)', 'сборник (various)', 'various (сборник)'];
    public static $toReplace = 'Various (Сборник)';
    private static $various = false; // признак что исполнитель системное значение Various т.е Сборник
    private $artistIds;
    private $query;
    const   DISCOGS_SYSTEM_ID = 194; // id для Various артистов


    public static function getDiscogApi()
    {
        if (self::$discogApi) {
            return self::$discogApi;
        } else {
            return new Orin(Config::get('discogs'));
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
        if ($images) {
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
        }
        $item->cdn_count_images = --$i;
        $item->save();
        //очистка временной папки discogs
        if (is_dir(storage_path('app/public/discogs/tmp/'))) {
            rrmdir(storage_path('app/public/discogs/tmp/'));
        }
    }


    public static function maybeVarious($author)
    {
        if (in_array(mb_strtolower($author), self::$whatReplace)) {
            self::$various = true;
            return self::$toReplace;
        }
        return '';
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

    private static function errorReport($message)
    {
        return ['error' => $message];
    }

    public static function getArtistsLink($discogsArtistIds)
    {
        if ($discogsArtistIds) {
            $systemId = DiscogsService::DISCOGS_SYSTEM_ID;
            if ($discogsArtistIds == $systemId) return self::$toReplace;
            if ($discogsArtistIds != $systemId) {
                $authors_ids = str_replace([',' . $systemId, $systemId . ','], '', $discogsArtistIds);
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

    public static function getReleases($data) {
        $params = [
            "format" => "Vinyl",
            "type" => "release"
        ];
        $query = $data['name'];
        if ($data['author']) {
            $various = self::maybeVarious($data['author']);
            if ($various) {
                $query = $various. ' - ' . $query;
            } else {
                $query = $data['author'] . ' - ' . $query;
            }
        }
        if ($data['year'] && $data['year'] > 1900) {
            $params['year'] = $data['year'];
        }
        $searchRelease = self::getDiscogApi()->search($query, $params);
        if ($searchRelease->status_code != 200) {
            return self::errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                print_r($searchRelease, true) . "\r\n" . print_r($data, true));
        } else {
            //если ничего не найдено и был указан год, убираем год из параметров и повторяем запрос к api discogs
            if (!$searchRelease->results && isset($params['year'])) {
                $notFound = $params['year'];
                unset($params['year']);
                $searchRelease = self::getDiscogApi()->search($query, $params);
                if ($searchRelease->status_code != 200) {
                    return self::errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        print_r($searchRelease, true) . "\r\n" . print_r($data, true));
                } elseif ($searchRelease->results) {
                    return ['releases' => $searchRelease->results, 'error_year' => 1];
                }
            } elseif ($searchRelease->results && isset($searchRelease->results)) {
                return ['releases' => $searchRelease->results];
            }
        }
        return ['releases' => []];
    }

    /**
     * Добавляет атристов и их релизы в базу по релизу пластинки
     * @param $releaseId
     * @return array
     */
    public static function updateArtistsAndReleasesData($releaseId)
    {
        if ($releaseId && is_numeric($releaseId)) {
            $result = self::getDiscogApi()->release($releaseId);
             if ($result->status_code == 200) {
                 if (is_array($result->artists) && count($result->artists)) {
                     $artistsIds = [];
                     $doubleArtist = [];
                     foreach ($result->artists as $artist) {
                         if (in_array($artist->id, $doubleArtist)) continue;
                         $doubleArtist[] = $artist->id;
                         $artistsIds[] = $artist->id;
                     }
                     if ($artistsIds) {
                         $artistsData = self::getArtistsData($artistsIds);
                         if (isset($artistsData['error'])) {
                             return $artistsData;
                         } else {
                             //добавляем новых исполнителей в базу и их фото в cdn
                             $updateArtists = self::updateArtistsInBase($artistsData);
                             foreach ($updateArtists as $artist) {
                                 $images = $artist->images;
                                 unset($artist->images);
                                 self::addImagesInCDN($images, $artist, $type = 'artist');
                             }
                             //добавляем релизы в базу
                             $updateReleases = self::updateArtistReleases($updateArtists);
                             if (isset($updateReleases['error'])) {
                                 return $updateReleases;
                             } else {
                                 //добавляем релизы в файл и чистим таблицу release
                                 $updateReleasesFileOnDis = self::uploadReleasesFileOnDisc($updateArtists);
                                 if (isset($updateReleasesFileOnDis['error'])) {
                                     return $updateReleasesFileOnDis;
                                 }
                             }
                         }
                     }
                     return $artistsIds;
                 } else {
                     return 0;
                 }
             } else {
                 return self::errorReport("Status Code не равен 200. Строка " . __LINE__);
             }
        } else {
            return self::errorReport("Релиз не найден " . $releaseId);
        }
    }


    /*
     * Добавляет исполнителей в таблицу discogs_artist,
     * поля releases и releases_file  обновляются после получения релизов
     * Возвращает массив объектов модели discogs_artist добавленных в базу
     */
    public static function updateArtistsInBase($artistData)
    {
        $artistsUpdate = [];
        foreach ($artistData as $key => $artist) {
            $findArtist = DiscogsArtist::select()->where('discogs_artist_id', $artist['artist_id'])->first();
            if ($findArtist) {
                continue;
            } else {
                $images = $artist['images'];
                unset($artist['images']);
                $artist['discogs_artist_id'] = $artist['artist_id'];
                unset($artist['artist_id']);
                $artistsUpdate[$key] =
                    DiscogsArtist::firstOrCreate(['discogs_artist_id' => $artist['discogs_artist_id']], $artist);
                $artistsUpdate[$key]->images = $images;
            }
        }
        return $artistsUpdate;
    }

    public static function updateArtistReleases($artists)
    {
        foreach ($artists as $artist) {
            $artistFromApi = self::getDiscogApi()->artist($artist->discogs_artist_id);
            if ($artistFromApi->status_code != 200) {
                return self::errorReport("Код не равен 200 при получении данных atrist Id:" . $artist->id);
            } else {
                $artistName = $artistFromApi->name;
            }
            $result = self::getDiscogApi()->search("", ['per_page' => 500, 'format' => 'Vinyl',
                'artist'=> $artistName, 'type' => 'release']);
            if ($result->status_code != 200) {
                return self::errorReport("Код не равен 200 при получении данных releases atrist " .
                    $artist->id . " : " . $artistName);
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
                        return self::errorReport("In update releases artistId: "
                            . $artist->id . " not found params: " . implode(", ", $listFalseValues));
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
        return true;
    }

    public static function uploadReleasesFileOnDisc($artists)
    {
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
                return self::errorReport("Не удалось создать путь для релиза artistId " . $artist->id);
            }
        }
        return true;
    }

    public static function getArtistsData($artistsIds)
    {
        try {
            $artistList = [];
            $doubleArtist = [];
            foreach ($artistsIds as $key => $artistId) {
                if (in_array($artistId, $doubleArtist)) {
                    continue;
                }
                $doubleArtist[] = $artistId;
                if ($artistId == self::DISCOGS_SYSTEM_ID) {
                    $artistList[$key]['artist_id'] = self::DISCOGS_SYSTEM_ID;
                    continue;
                }
                $artist = self::getDiscogApi()->artist($artistId);
                if ($artist->status_code !== 200) {
                    return self::errorReport("Ошибка на строке " . __LINE__ . " код ответа не равен 200 \r\n" .
                        "ArtistId: " . $artistId . "\r\n");
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
                        return self::errorReport("In artistId: " . $artistId . " not found params: " .
                            implode(", ", $listFalseValues));
                    } else {
                        $artistList[$key] = $data;
                    }
                }
            }
            return $artistList;
        } catch (ClientException $e) {
            return self::errorReport("Exception на строке " . __LINE__ . "\r\n Message: " . $e->getMessage());
        }
    }
}
?>

