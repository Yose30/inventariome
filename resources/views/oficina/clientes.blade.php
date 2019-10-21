@extends('layouts.app')

@section('content')
    <clientes-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$clientes}}"></clientes-component>
@endsection