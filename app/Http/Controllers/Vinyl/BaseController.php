<?php

namespace App\Http\Controllers\Vinyl;

use App\Http\Controllers\Controller;
use App\Services\VinylService;

class BaseController extends Controller
{
   public $service;

   public function __construct(VinylService $service) {
       $this->service = $service;
   }
}
