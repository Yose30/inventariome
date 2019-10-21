@extends('layouts.app')

@section('content')
    <remisiones-component :registersall="{{$remisiones}}"></remisiones-component>
@endsection
