@extends('layouts.app')

@section('content')
    <vendidos-component :registersall="{{$datos}}" :editoriales="{{$editoriales}}"></vendidos-component>
@endsection