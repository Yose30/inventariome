@extends('layouts.app')

@section('content')
    <adeudos-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$adeudos}}"></adeudos-component>
@endsection