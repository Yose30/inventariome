<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectPath(){
        if(auth()->user()->role_id == 1){
            return '/administrador/remisiones';
        }
        if(auth()->user()->role_id == 2){
            return '/oficina/remisiones';
        }
        if(auth()->user()->role_id == 3){
            return '/almacen/entregas';
        }
        if(auth()->user()->role_id == 4){
            return '/gestor/remisiones';
        }
    } 
    //METODOS SOBREESCRITOS
    public function username(){
        return 'user_name';
    }

    //Metodo logout para cerrar sesion, y nos ubique en la vista login
    public function logout(){
        auth()->logout(); //Para poder cerrar sesión de la aplicación
        session()->flush(); //Limpiar todas las sesiones
        return redirect('/login');
    }
}
