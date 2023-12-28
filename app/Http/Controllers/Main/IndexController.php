<?php

namespace App\Http\Controllers\Main;

//use DB;
//use Illuminate\Support\Facades\Hash;

use App\Exports\UserAdvertsExport;
use App\Mail\SellRecords;
use App\Models\Advert;
use App\Models\AyBy;
use App\Models\Page;
use App\Models\Style;
use App\Models\User;
use App\Services\AdvertService;
use App\Services\ProfileService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class IndexController extends BaseController
{
    public function __invoke($url = 'home')
    {
     //   $profileSevert = new ProfileService();
      // dd($profileSevert->createAdvertThumbnail('/rule/main.jpg'));
   //     DB::table('users')->insert(['name'=>'Egor','email'=>'bonker85@mail.ru','password'=>Hash::make('fishki182')]);
        if ($url == 'sell-records') {
            return view('sell-records');
        }
        $page = $this->service->getPage($url);
        $lastAdvertsList = Advert::select()
            ->where('status', AdvertService::getStatusByName('activated'))
            ->orderBy('up_time', 'DESC')->limit(8)->get();
        $styles = Style::select()->orderBy('name')->get();
        return view('main.index', compact('page', 'lastAdvertsList', 'styles'));
    }

    public function sellRecords()
    {
        if (request()->message) {
            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new SellRecords(['message' => request()->message]));
        }
        return view('sell-records', ['success' => true]);
    }

    public function vinylList(Request $request)
    {
        $advertQuery = Advert::select('adverts.*', 's.name AS sname')
            ->where('status', 1)
            ->join('styles AS s', 's.id', '=', 'adverts.style_id')
            ->where('user_id', 6)
            ->where('author', '!=', '');
        if ($request->post()) {
            if ($request->author) {
                $author = '%' . str_replace("\"", "'", strtolower(trim($request->author))) . '%';
                $advertQuery->where(DB::raw("LCASE(author)"), 'LIKE', DB::raw("LCASE(\"".$author."\")"));
            }
            if ($request->name) {
                $name = '%' . str_replace("\"", "'", strtolower(trim($request->name))) . '%';
                $advertQuery->where(DB::raw("LCASE(adverts.name)"), 'LIKE', DB::raw("LCASE(\"".$name."\")"));
            }
            if ($request->sku) {
                $advertQuery->where('sku', $request->sku);
            }
            if ($request->uid) {
                $advertQuery->where('uid', $request->uid);
            }
        }
        $adverts = $advertQuery
            ->orderBy('author')->paginate(500);
        return view('main.vinyl-list', compact('adverts'));
    }


    public function ayList(Request $request)
    {
        if (!User::isMyUsers()) {
            abort(404);
        }
        $advertQuery = AyBy::orderBy('view', 'ASC');
        if ($request->post()) {
            if (!empty($request->hide)) {
                AyBy::whereIn('id', $request->hide)->update(
                    ['hide' => 1]
                );
            }
            if (!empty($request->view)) {
                AyBy::whereIn('id', $request->view)->update(
                    ['view' => 1]
                );
            }
        }
        if ($request->query()) {
                if ($request->typeId) {
                    $advertQuery->whereIn('type', $request->typeId);
                }
                if ($request->updated_price) {
                    $advertQuery->where('updated_price', 1)
                        ->whereRaw('price_hot < price_hot_old');
                }
                if ($request->author) {
                    $author= '%' . str_replace("\"", "'", strtolower(trim($request->author))) . '%';
                    $advertQuery->where(DB::raw("LCASE(author)"), 'LIKE', DB::raw("LCASE(\"" . $author . "\")"));
                }
                if ($request->title) {
                    $title = '%' . str_replace("\"", "'", strtolower(trim($request->title))) . '%';
                    $advertQuery->where(DB::raw("LCASE(title)"), 'LIKE', DB::raw("LCASE(\"" . $title . "\")"));
                }
                if ($request->priceMin) {
                    $advertQuery->where('price_hot','>=', $request->priceMin);
                }
            if ($request->priceMax) {
                $advertQuery->where('price_hot','<=', $request->priceMax);
            }
            /*
            if ($request->name) {
                $name = '%' . str_replace("\"", "'", strtolower(trim($request->name))) . '%';
                $advertQuery->where(DB::raw("LCASE(adverts.name)"), 'LIKE', DB::raw("LCASE(\"".$name."\")"));
            }
            if ($request->sku) {
                $advertQuery->where('sku', $request->sku);
            }
            if ($request->uid) {
                $advertQuery->where('uid', $request->uid);
            }*/
        }
        if (!$request->with_hide) {
            $advertQuery->where('hide', 0);
        }
        $adverts = $advertQuery->paginate(500);
        return view('main.ay-list', compact('adverts'));
    }

    public function createExcelForVinilCD()
    {
        $data = [
            'users_ids' => [6],
            'sep' => 'none'
        ];
        return (new UserAdvertsExport($data))->download('vinyl.xlsx');
    }

}
