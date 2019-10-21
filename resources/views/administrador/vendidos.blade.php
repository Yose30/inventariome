@extends('layouts.app')

@section('content')
    <vendidos-component :registersall="{{$datos}}"></vendidos-component>
@endsection