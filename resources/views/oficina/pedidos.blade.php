@extends('layouts.app')

@section('content')
    <compras-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$compras}}"></compras-component>
@endsection