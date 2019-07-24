@extends('layouts.app')

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a 
                    class="nav-link active" 
                    id="movimientos-tab" 
                    data-toggle="tab" 
                    href="#v-movimientos" 
                    aria-controls="v-movimientos">
                    {{ __("Crear remisión") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="remisiones-tab" 
                    data-toggle="tab" 
                    href="#v-remisiones" 
                    aria-controls="v-remisiones">
                    {{ __("Remisiones") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="clientes-tab" 
                    data-toggle="tab" 
                    href="#v-clientes" 
                    aria-controls="v-clientes">
                    {{ __("Clientes") }}
                </a>
            </li>
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
            <div class="tab-pane fade show active" id="v-movimientos">
                <br>
                <remision-component></remision-component>
            </div>
            <div class="tab-pane fade" id="v-remisiones">
                <br>
                <lista-remisiones-component></lista-remisiones-component>
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component :role_id="{{ auth()->user()->role_id }}"></libros-component>
            </div>
            <div class="tab-pane fade" id="v-clientes">
                <br>
                <clientes-component></clientes-component>
            </div>
        </div>
    </div>
@endsection
