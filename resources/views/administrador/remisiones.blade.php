@extends('layouts.app')

@section('content')
    <listado-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$remisiones}}"></listado-component>
@endsection