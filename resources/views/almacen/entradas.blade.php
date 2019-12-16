@extends('layouts.app')

@section('content')
    <entradas-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$entradas}}" :editoriales="{{$editoriales}}"></entradas-component>
@endsection