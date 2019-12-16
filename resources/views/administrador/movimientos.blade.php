@extends('layouts.app')

@section('content')
    <movimientos-component :editoriales="{{$editoriales}}"></movimientos-component>
@endsection