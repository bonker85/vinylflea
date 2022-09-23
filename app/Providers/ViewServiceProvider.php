<?php

namespace App\Providers;
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
        View::composer(["includes.left-sidebar", "includes.main-menu"], function ($view) {
           // $catalog = Catalog::where('status', 1)->orderBy('position')->where('parent_id', 0)->get();
         //   $view->with('catalog', $catalog);
          //  $view->with('route_name', request()->route()->getName());
        });

        /********************** PROFILE ***********************************************/
        View::composer(["includes.profile-menu"], function($view) {
            $view->with('route_name', request()->route()->getName());
        });
        /********************** BACKEND ***********************************************/
        /* боковое меню админ панели вывод каталога */
        View::composer(["admin.includes.sidebar"], function ($view) {
            $view->with('route_prefix', request()->route()->getPrefix());
        });

    }
}
