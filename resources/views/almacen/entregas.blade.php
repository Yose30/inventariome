@extends('layouts.app')

@section('content')
    <remisiones-component :registersall="{{$remisiones}}" :listresponsables="{{$responsables}}"></remisiones-component>
@endsection
