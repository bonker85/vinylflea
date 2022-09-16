<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function __construct(Request $request)
    {
       if (!$request->ajax()) {
           abort('404');
       }
    }

    public function index(Request $request, $param)
    {
        switch($param) {
            case 'translate_url':
                if ($request->name) {
                    return translate_url($request->name);
                } else {
                    return "";
                }
                break;
            case 'get_pages_nodes':
                $pageService = new PageService();
                return $pageService->getPagesTree();
                break;
            case 'update_page_position':
                $pageService = new PageService();
                return $pageService->updatePagePosition($request->all());
                break;
            default:
                abort('404');
                break;
        }
    }
}
