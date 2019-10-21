@extends('layouts.app')

@section('content')
    <devolucion-adeudos-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$adeudos}}"></devolucion-adeudos-component>
@endsection