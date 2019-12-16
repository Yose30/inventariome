@extends('layouts.app')

@section('content')
    <libros-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$libros}}" :editoriales="{{$editoriales}}"></libros-component>
@endsection