<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Services\PageService;

class BaseController extends Controller
{
   public $service;

   public function __construct(PageService $service) {
       $this->service = $service;
   }
}
