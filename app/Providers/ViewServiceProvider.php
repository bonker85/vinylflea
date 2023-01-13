<?php

namespace App\Providers;
use App\Models\Advert;
use App\Models\AdvertDialog;
use App\Models\AdvertFavorit;
use App\Models\Page;
use App\Models\Style;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /********************** FRONTAND ***********************************************/
        /* Главное и боковое меню сайта, вывод товаров из каталога */
        View::composer(["includes.main-menu", 'includes.search-block'], function ($view) {
            $styles = Style::select()->where('count', '!=', 0)->orderBy('name')->get();
            $view->with('styles', $styles);
           // $catalog = Catalog::where('status', 1)->orderBy('position')->where('parent_id', 0)->get();
         //   $view->with('catalog', $catalog);
          //  $view->with('route_name', request()->route()->getName());
        });
        View::composer(['includes.popular-block'], function($view) {
            $styles = Style::select()->whereRaw('slug IN ("jazz", "pop", "rock")')->get();
            $view->with('popular_styles', $styles);
        });
        View::composer(['includes.sell-faster-block'], function ($view) {
            $adverts = Advert::select()->where('user_id', 11)
                ->where('status', 1)->inRandomOrder()->limit(12)->get();
            $view->with('sellFasterAdverts', $adverts);
        });
        View::composer('*', function ($view) {
            $view->with('admin', User::isAdmin());
        });
        View::composer(['includes.last-news-block'], function($view) {
            $lastNewsList = Page::select()->where('status', 1)->where('parent_id', 2)
                ->orderBy('position')->limit(8)->get();
            $view->with('lastNewsList', $lastNewsList);
        });
        View::composer(["includes.advert-block"], function($view) {
            $favoritUserAdvertsList = [];
            if (auth()->check()) {
                $favoritUserAdvertsList = AdvertFavorit::select('advert_id')
                    ->where('user_id', auth()->user()->id)->pluck('advert_id')->toArray();
            }
            $view->with('favoritUserAdvertsList', $favoritUserAdvertsList);
        });
        /********************** PROFILE ***********************************************/
        View::composer(["includes.profile-menu", "includes.main-menu", "includes.message"], function($view) {
            // количество непрочитанных сообщений
            if (auth()->check()) {
                $userId = auth()->user()->id;
                $resSum = advertDialog::selectRaw(' SUM(IF (from_user_id=' . $userId .
                    ', count_not_view_user_from, IF (to_user_id=' . $userId . ', count_not_view_user_to, 0))) AS sum')
                    ->where(function($query) {
                        $query->where('from_user_id', auth()->user()->id)
                            ->where('count_not_view_user_from', '>', 0);
                    })
                    ->orWhere(function($query) {
                        $query->where('to_user_id', auth()->user()->id)
                            ->where('count_not_view_user_to', '>', 0);
                    })
                    ->first();
            } else {
                $resSum = 0;
            }

            $countViewMessages = 0;
            if ($resSum) {
                $countViewMessages = $resSum->sum;
            }
            $view->with([
                'countViewMessages' => $countViewMessages,
                'route_name' =>  request()->route()?->getName()
            ]);
        });
        /********************** BACKEND ***********************************************/
        /* боковое меню админ панели вывод каталога */
        View::composer(["admin.includes.sidebar"], function ($view) {
            $view->with('route_prefix', request()->route()->getPrefix());
        });

    }
}
