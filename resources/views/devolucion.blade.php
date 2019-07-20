@extends('layouts.app')

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a 
                    class="nav-link active" 
                    id="devolucion-tab" 
                    data-toggle="tab" 
                    href="#v-devolucion" 
                    aria-controls="v-devolucion">
                    {{ __("Registrar devolución") }}
                </a>
            </li>
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
                    id="newlibro-tab" 
                    data-toggle="tab" 
                    href="#v-newlibro" 
                    aria-controls="v-newlibro">
                    {{ __("Nuevo libro") }}
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="v-devolucion">
                <br>
                <devolucion-component></devolucion-component>
            </div>
            <div class="tab-pane fade" id="v-entrada">
                <br>
                <inventario-component></inventario-component>
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component></libros-component>
            </div>
            <div class="tab-pane fade" id="v-newlibro">
                <br>
                <new-libro-component></new-libro-component>
            </div>
        </div>
    </div>
@endsection
