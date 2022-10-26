<?php
namespace App\Services\Utility;
use VK\Client\VKApiClient;

class VkService {
    //https://oauth.vk.com/authorize?client_id=51459391&display=page&redirect_uri=https://api.vk.com/blank.html&scope=offline,wall,photos&response_type=code
    //получил код c405cf670b27ba0440
    //https://oauth.vk.com/access_token?client_id=51459391&client_secret=MsFxZMHXuTuWy6C0ctef&redirect_uri=https://api.vk.com/blank.html&code=c405cf670b27ba0440
    //access token
    /*
     * {"access_token":"vk1.a.KtQ12M1JLh4dJsfQA5T-dXOu9lTNy7wRkWpVCTQFzZjaBxRRa0VpTLeldKctBxa1iYcJg2UzGspEtxotyQ61cv6zk6peANeaiH_t0KFv5u6yigymFM5X6tx35loZYhSilRlAyGHQrLA13IiJxO-orr2fwVIDF1aNoV05c2Mg-Kd17mlOr8-mQQO7o632NH7GjHU204OnnB3n9WL8TKru5A","expires_in":0,"user_id":327384215}
     */
    public function __construct()
    {
/*        $token = 'vk1.a.KtQ12M1JLh4dJsfQA5T-dXOu9lTNy7wRkWpVCTQFzZjaBxRRa0VpTLeldKctBxa1iYcJg2UzGspEtxotyQ61cv6zk6peANeaiH_t0KFv5u6yigymFM5X6tx35loZYhSilRlAyGHQrLA13IiJxO-orr2fwVIDF1aNoV05c2Mg-Kd17mlOr8-mQQO7o632NH7GjHU204OnnB3n9WL8TKru5A';
        $vk = new VKApiClient();
        dd($vk->wall()->post($token, ['owner_id'=> '-195454641','message' => '**Осторожно**', 'from_group' => '1']));*/
    }

}
?>
