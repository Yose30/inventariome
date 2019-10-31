@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fa fa-exclamation-triangle"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <listado-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$remisiones}}"></listado-component>
@endsection