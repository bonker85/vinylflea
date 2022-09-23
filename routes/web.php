<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('layouts.app');
});*/
Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin_panel', 'middleware' => ['auth', 'admin']], function() {
    Route::group(['namespace' => 'Main'], function () {
        Route::get('/', 'IndexController')->name('admin.main');
    });

    Route::group(['namespace' => 'User', 'prefix' => 'users'], function () {
        Route::get('/', 'IndexController')->name('admin.user.index');
        Route::get('/create', 'CreateController')->name('admin.user.create');
        Route::post('/', 'StoreController')->name('admin.user.store');
        Route::get('/{user}', 'ShowController')->name('admin.user.show');
        Route::get('edit/{user}', 'EditController')->name('admin.user.edit');
        Route::patch('/{user}', 'UpdateController')->name('admin.user.update');
        Route::delete('/{user}', 'DeleteController')->name('admin.user.delete');
    });

    Route::group(['namespace' => 'Page', 'prefix' => 'pages'], function () {
        Route::get('/', 'IndexController')->name('admin.page.index');
        Route::get('/create', 'CreateController')->name('admin.page.create');
        Route::post('/', 'StoreController')->name('admin.page.store');
        Route::get('/{page}', 'ShowController')->name('admin.page.show');
        Route::get('edit/{page}', 'EditController')->name('admin.page.edit');
        Route::patch('/{page}', 'UpdateController')->name('admin.page.update');
        Route::delete('/{page}', 'DeleteController')->name('admin.page.delete');
        Route::delete('destroy/{pages?}', 'DestroyController')->name('admin.page.destroy');
    });
    Route::match(['post', 'get'],'ajax/{param}', 'AjaxController@index')->name('admin.ajax');
});


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Ссылка успешно отправлена. Проверьте Ваш Email');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::match(['post', 'get'],'ajax/{param}', 'App\Http\Controllers\AjaxController@index')->name('main.ajax');

Route::group(['namespace' => 'App\Http\Controllers\Profile', 'prefix' => 'profile', 'middleware' => ['auth', 'verified']], function() {
    Route::get('/', 'IndexController@index')->name('profile.index');
    Route::match(['get', 'post'], '/settings', 'IndexController@settings')->name('profile.settings');
});
Route::match(['get', 'post'], 'tasks/{param}', 'App\Http\Controllers\TasksController@index')->name('tasks');
Route::group(['namespace' => 'App\Http\Controllers\Main'], function() {
    Route::get('/{url?}', 'IndexController')->name('main.index');
});
