@extends('layouts.app')

<style>
    #scroll-c{
        overflow:auto; 
        height:550px;
    }
</style>

@section('content')
<div class="container">
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a 
                    class="nav-link active" 
                    id="remisiones-tab" 
                    data-toggle="tab" 
                    href="#v-remisiones" 
                    aria-controls="v-remisiones">
                    {{ __("Remisiones") }}
                </a>
            </li>
            <!-- <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="devoluciones-tab" 
                    data-toggle="tab" 
                    href="#v-devoluciones" 
                    aria-controls="v-devoluciones">
                    {{ __("Devoluciones") }}
                </a>
            </li> -->
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="libros-tab" 
                    data-toggle="tab" 
                    href="#v-libros" 
                    aria-controls="v-libros">
                    {{ __("Libros") }}
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="v-remisiones">
                <br>
                <listado-component></listado-component>
            </div>
            <div class="tab-pane fade" id="v-devoluciones">
                <br>
                
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component></libros-component>
            </div>
        </div>
    </div>
</div>
@endsection
