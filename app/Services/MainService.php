<?php
namespace App\Services;

use App\Models\Page;

class MainService {

        public function getPage($url) {
            if(empty($url)) {
                $url = "home";
            }

            $page = Page::where('url', $url)->first();
            if ($page) {
               return $page;
            } else {
               abort('404');
            }
        }
}
?>
