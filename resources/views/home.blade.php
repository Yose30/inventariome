@extends('layouts.app')

<style>
    #scroll-c{
        overflow:auto; 
        height:550px;
    }
</style>

@section('content')
<div class="container">
<a 
                        class="btn btn-info"
                        href="{{ route('imprimirSalida') }}">
                        <i class="fa fa-print"></i>
                    </a>
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body" id="scroll-c">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" aria-orientation="vertical">
                        <a 
                            class="nav-link" 
                            id="movimientos-tab" 
                            data-toggle="pill" 
                            href="#v-movimientos" 
                            aria-controls="v-movimientos">
                            {{ __("Crear remisión") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="devolucion-tab" 
                            data-toggle="pill" 
                            href="#v-devolucion" 
                            aria-controls="v-devolucion">
                            {{ __("Registrar devolucion") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="listado-tab" 
                            data-toggle="pill" 
                            href="#v-listado" 
                            aria-controls="v-listado">
                            {{ __("Generar reporte") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="new_client-tab" 
                            data-toggle="pill" 
                            href="#v-new_client" 
                            aria-controls="v-new_client">
                            {{ __("Nuevo cliente") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="new_libro-tab" 
                            data-toggle="pill" 
                            href="#v-new_libro" 
                            aria-controls="v-new_libro">
                            {{ __("Nuevo libro") }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body" id="scroll-c">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div 
                            class="tab-pane fade" 
                            id="v-listado">
                            <listado-component></listado-component>
                        </div>
                        <div 
                            class="tab-pane fade" 
                            id="v-movimientos">
                            <remision-component></remision-component>
                        </div>
                        <div 
                            class="tab-pane fade" 
                            id="v-devolucion">
                            <devolucion-component></devolucion-component>
                        </div>
                        <div 
                            class="tab-pane fade" 
                            id="v-new_client">
                            <new-client-component></new-client-component>
                        </div>
                        <div 
                            class="tab-pane fade" 
                            id="v-new_libro">
                            <new-libro-component></new-libro-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
