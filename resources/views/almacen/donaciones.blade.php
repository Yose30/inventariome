@extends('layouts.app')

@section('content')
    <donaciones-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$regalos}}" :listresponsables="{{$responsables}}"></donaciones-component>
@endsection