@extends('layouts.app')

@section('content')
    <remision-component :registersall="{{$clientes}}"></remision-component>
@endsection