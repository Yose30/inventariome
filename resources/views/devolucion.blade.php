@extends('layouts.app')

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <!-- <li class="nav-item">
                <a 
                    class="nav-link active" 
                    id="entrega-tab" 
                    data-toggle="tab" 
                    href="#v-entrega" 
                    aria-controls="v-entrega">
                    {{ __("Entrega de libros") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="devoluciones-tab" 
                    data-toggle="tab" 
                    href="#v-devoluciones" 
                    aria-controls="v-devoluciones">
                    {{ __("Pagos") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="devolucion-tab" 
                    data-toggle="tab" 
                    href="#v-devolucion" 
                    aria-controls="v-devolucion">
                    {{ __("Devoluciones") }}
                </a>
            </li> -->
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="entrada-tab" 
                    data-toggle="tab" 
                    href="#v-entrada" 
                    aria-controls="v-entrada">
                    {{ __("Registrar entrada") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="newlibro-tab" 
                    data-toggle="tab" 
                    href="#v-newlibro" 
                    aria-controls="v-newlibro">
                    {{ __("Agregar libro") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="entradas-tab" 
                    data-toggle="tab" 
                    href="#v-entradas" 
                    aria-controls="v-entradas">
                    {{ __("Entradas") }}
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
            <div class="tab-pane fade show active" id="v-entrega">
                <br>
                <remisiones-component></remisiones-component>
            </div>
            <div class="tab-pane fade" id="v-devolucion">
                <br>
                <devolucion-component></devolucion-component>
            </div>
            <div class="tab-pane fade" id="v-devoluciones">
                <br>
                <pagos-component></pagos-component>
            </div>
            <div class="tab-pane fade" id="v-entrada">
                <br>
                <inventario-component></inventario-component>
            </div>
            <div class="tab-pane fade" id="v-entradas">
                <br>
                <entradas-component></entradas-component>
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component :role_id="{{ auth()->user()->role_id }}"></libros-component>
            </div>
            <div class="tab-pane fade" id="v-newlibro">
                <br>
                <new-libro-component></new-libro-component>
            </div>
        </div>
    </div>
@endsection
