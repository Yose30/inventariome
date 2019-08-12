@extends('layouts.app')

@section('content')
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a 
                    class="nav-link active" 
                    id="entrega-tab" 
                    data-toggle="tab" 
                    href="#v-entrega" 
                    aria-controls="v-entrega">
                    {{ __("Remisiones") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="vendidos-tab" 
                    data-toggle="tab" 
                    href="#v-vendidos" 
                    aria-controls="v-vendidos">
                    {{ __("Vendidos") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="remisiones-tab" 
                    data-toggle="tab" 
                    href="#v-remisiones" 
                    aria-controls="v-remisiones">
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
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="notas-tab" 
                    data-toggle="tab" 
                    href="#v-notas" 
                    aria-controls="v-notas">
                    {{ __("Notas") }}
                </a>
            </li>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="promociones-tab" 
                    data-toggle="tab" 
                    href="#v-promociones" 
                    aria-controls="v-promociones">
                    {{ __("Promociones") }}
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
            <div class="tab-pane fade" id="v-vendidos">
                <br>
                <vendidos-component></vendidos-component>
            </div>
            <div class="tab-pane fade" id="v-remisiones">
                <br>
                <pagos-component></pagos-component>
            </div>
            <div class="tab-pane fade" id="v-devolucion">
                <br>
                <devolucion-component></devolucion-component>
            </div>
            <div class="tab-pane fade" id="v-notas">
                <br>
                <new-nota-component :role_id="{{ auth()->user()->role_id }}"></new-nota-component>
            </div>
            <div class="tab-pane fade" id="v-promociones">
                <br>
                <promociones-component :role_id="{{ auth()->user()->role_id }}"></promociones-component>
            </div>
            <div class="tab-pane fade" id="v-entradas">
                <br>
                <entradas-component :role_id="{{ auth()->user()->role_id }}"></entradas-component>
            </div>
            <div class="tab-pane fade" id="v-libros">
                <br>
                <libros-component :role_id="{{ auth()->user()->role_id }}"></libros-component>
            </div>
            <div class="tab-pane fade" id="v-adeudos">
                <br>
                <devolucion-adeudos-component :role_id="{{ auth()->user()->role_id }}"></devolucion-adeudos-component>
            </div>
        </div>
    </div>
@endsection
