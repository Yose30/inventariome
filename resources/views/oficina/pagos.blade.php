@extends('layouts.app')

@section('content')
    <pagos-component :role_id="{{auth()->user()->role_id}}" :registersall="{{$remisiones}}"></pagos-component>
@endsection