@extends('layouts.app')

@section('content')
    <list-general-component :registersall="{{$remisiones}}"></list-general-component>
@endsection