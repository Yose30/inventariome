@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div 
                        class="nav flex-column nav-pills" id="v-pills-tab" aria-orientation="vertical">
                        <a 
                            class="nav-link" 
                            id="movimientos-tab" 
                            data-toggle="pill" 
                            href="#v-movimientos" 
                            aria-controls="v-movimientos">
                            {{ __("Generar remisión") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="new_client-tab" 
                            data-toggle="pill" 
                            href="#v-new_client" 
                            aria-controls="v-new_client">
                            {{ __("Agregar cliente") }}
                        </a>
                        <a 
                            class="nav-link" 
                            id="new_libro-tab" 
                            data-toggle="pill" 
                            href="#v-new_libro" 
                            aria-controls="v-new_libro">
                            {{ __("Agregar libro") }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div 
                            class="tab-pane fade" 
                            id="v-movimientos">
                            <remision-component></remision-component>
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
