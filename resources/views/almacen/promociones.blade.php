@extends('layouts.app')

@section('content')
    <promociones-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$promotions}}" :listresponsables="{{$responsables}}"></promociones-component>
@endsection