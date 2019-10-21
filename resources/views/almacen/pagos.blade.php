@extends('layouts.app')

@section('content')
    <devolucion-component :registersall="{{$remisiones}}"></devolucion-component>
@endsection