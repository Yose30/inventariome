<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role){
        if(auth()->check() && auth()->user()->role->rol === $role){
            // abort(401, __("No puedes acceder a este sitio"));
            return $next($request);
        }
        if(auth()->user()->role_id === 1){
            return redirect()->route('administrador.remisiones');
        }
        if(auth()->user()->role_id === 2){
            return redirect()->route('oficina.remisiones');
        }
        if(auth()->user()->role_id === 3){
            return redirect()->route('almacen.entregas');
        }
    }
}
