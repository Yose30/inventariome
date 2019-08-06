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
                    id="new-cliente-tab" 
                    data-toggle="tab" 
                    href="#v-new-cliente" 
                    aria-controls="v-new-cliente">
                    {{ __("Agregar cliente") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="pagos-tab" 
                    data-toggle="tab" 
                    href="#v-pagos" 
                    aria-controls="v-pagos">
                    {{ __("Pagos") }}
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
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="adeudos-tab" 
                    data-toggle="tab" 
                    href="#v-adeudos" 
                    aria-controls="v-adeudos">
                    {{ __("Adeudos") }}
                </a>
            </li>
            <!-- <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="entradas-tab" 
                    data-toggle="tab" 
                    href="#v-entradas" 
                    aria-controls="v-entradas">
                    {{ __("Entradas") }}
                </a>
            </li> -->
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="v-movimientos">
                <br>
                <remision-component></remision-component>
            </div>
            <div class="tab-pane fade" id="v-remisiones">
                <br>
                <listado-component :role_id="{{auth()->user()->role_id}}"></listado-component>
            </div>
            <div class="tab-pane fade" id="v-pagos">
                <br>
                <pagos-component></pagos-component>
            </div>
            <div class="tab-pane fade" id="v-new-cliente">
                <br>
                <new-client-component></new-client-component>
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component :role_id="{{ auth()->user()->role_id }}"></libros-component>
            </div>
            <div class="tab-pane fade" id="v-clientes">
                <br>
                <clientes-component :role_id="{{ auth()->user()->role_id }}"></clientes-component>
            </div>
            <div class="tab-pane fade" id="v-adeudos">
                <br>
                <adeudos-component></adeudos-component>
            </div>
            <div class="tab-pane fade" id="v-entradas">
                <br>
                <entradas-component :role_id="{{ auth()->user()->role_id }}"></entradas-component>
            </div>
        </div>
    </div>
@endsection
