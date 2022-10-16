<?php

namespace App\Providers;
use App\Models\AdvertDialog;
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
            $styles = Style::select()->orderBy('name')->get();
            $view->with('styles', $styles);
           // $catalog = Catalog::where('status', 1)->orderBy('position')->where('parent_id', 0)->get();
         //   $view->with('catalog', $catalog);
          //  $view->with('route_name', request()->route()->getName());
        });
        View::composer('*', function ($view) {
            $view->with('admin', User::isAdmin());
        });
        /********************** PROFILE ***********************************************/
        View::composer(["includes.profile-menu", "includes.main-menu"], function($view) {
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
                'route_name' =>  request()->route()->getName()
            ]);
        });
        /********************** BACKEND ***********************************************/
        /* боковое меню админ панели вывод каталога */
        View::composer(["admin.includes.sidebar"], function ($view) {
            $view->with('route_prefix', request()->route()->getPrefix());
        });

    }
}
