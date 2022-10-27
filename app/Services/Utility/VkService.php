<?php
namespace App\Services\Utility;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;

class VkService {
    //https://oauth.vk.com/authorize?client_id=51459391&display=page&redirect_uri=https://api.vk.com/blank.html&scope=offline,wall,photos&response_type=code
    //получил код c405cf670b27ba0440
    //https://oauth.vk.com/access_token?client_id=51459391&client_secret=MsFxZMHXuTuWy6C0ctef&redirect_uri=https://api.vk.com/blank.html&code=c405cf670b27ba0440
    private $vk;
    private $token;
    private $owner_id;
    private $album_id;

    public function __construct($owner_id = '195454641', $album_id = '272678514')
    {
        $this->vk = new VKApiClient();
        $this->token = env('VK_TOKEN');
        $this->owner_id = $owner_id;
        $this->album_id = $album_id;

    }
    public function addPost($message, $photos = []) {
        if ($photos) {
            $at = "";
            foreach ($photos as $item) {
                $at .= "photo-" . $this->owner_id . '_' . $item['id']. ',';
            }
            $at = rtrim($at, ",");
        }
        return $this->vk->wall()->post($this->token,
            [
                'owner_id'=> '-' . $this->owner_id,
                'message' => $message,
                'from_group' => '1',
                'attachments' => $at
            ]);
    }
    public function addPhotos($pathList)
    {
        if ($pathList) {
            $uploadServerResult =
                $this->vk->photos()->getUploadServer($this->token, [
                    'group_id' => $this->owner_id,
                    'album_id' => $this->album_id
                ]);
            if (is_array($uploadServerResult) && isset($uploadServerResult['upload_url'])) {
                $upload_url = $uploadServerResult['upload_url'];
                $i = 1;
                $multipart = [];
                foreach ($pathList as $path) {
                    if (file_exists($path)) {
                        array_push($multipart, [
                            'name'     => 'file' . $i,
                            'contents' => fopen($path, 'r'),
                            'filename' => $i . '.' . pathinfo($path, PATHINFO_EXTENSION)
                        ]);

                    } else {
                        return [
                            "error" => 1,
                            "message" => "Method: " . __METHOD__ . ": File " . $path . " dont exist on disc"
                        ];
                    }
                    $i++;
                }
                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', $upload_url, ['multipart' => $multipart]);
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    $responseBody = json_decode($response->getBody(), true);
                    if (is_array($responseBody) && isset($responseBody["photos_list"])) {
                        $result = json_decode($responseBody["photos_list"]);
                        if ($result) {
                            return [
                                "error" => 0,
                                "responseBody" => $responseBody
                            ];
                        } else {
                            return [
                                "error" => 1,
                                "message" => "Method: " . __METHOD__ . ': Photo List is empty'
                            ];
                        }
                    } else {
                        return [
                            "error" => 1,
                            "message" => "Method: " . __METHOD__ . ': GuzzleHttp responseBody return fail result <br/>'
                                . print_r($responseBody, true)
                        ];
                    }
                } else {
                    return [
                        "error" => 1,
                        "message" => "Method: " . __METHOD__ . ": GuzzleHttp responseCode not equal 200"
                    ];
                }

            } else {
                    return [
                        "error" => 1,
                        "message" => "Method: " . __METHOD__ . ": getUploadServer return bed response"
                    ];
            }

        } else {
            return [
                "error" => 1,
                "message" => "Method: " . __METHOD__ . ": Path List is empty"
            ];
        }
    }

    public function savePhotos($data)
    {
        $data['album_id'] = $this->album_id;
        $data['group_id'] = $this->owner_id;
        $result = $this->vk->photos()->save($this->token, $data);
        if (is_array($result) && count($result)) {
            return $result;
        } else {
            return false;
        }
    }


}
?>
