@extends('layouts.app')

@section('content')
    <libros-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$libros}}"></libros-component>
@endsection