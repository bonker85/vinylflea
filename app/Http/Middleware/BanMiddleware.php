<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (User::find(auth()->user()->id)->isBan()) {
            $request->session()->flash('error',
                'Ваш аккаунт (' . auth()->user()->email . ') добавлен в черный список администрацией сайта.');
            //Session::flush();

            Auth::logout();
            return redirect('/');
        }
        return $next($request);
    }
}
