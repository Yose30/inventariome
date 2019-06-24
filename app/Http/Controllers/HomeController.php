<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;
use App\Dato;
use App\Devolucione;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       return view('/home');
       // $remision = Remisione::whereid(1)->first();
        //$datos   = Dato::where('remision_id',$remision->id)->get();
        //return view('remision.nota',compact('remision','datos'));
        
    }

}
