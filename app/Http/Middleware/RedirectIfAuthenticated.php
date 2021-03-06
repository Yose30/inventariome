<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // return redirect('/home');
            if(Auth::user()->role_id == 1){
                return redirect('/administrador/remisiones');
            }
            if(Auth::user()->role_id == 2){
                return redirect('/oficina/remisiones');
            }
            if(Auth::user()->role_id == 3){
                return redirect('/almacen/entregas');
            }
            if(Auth::user()->role_id == 4){
                return redirect('/gestor/remisiones');
            }
        }

        return $next($request);
    }
}
