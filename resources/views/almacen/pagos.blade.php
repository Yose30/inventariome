@extends('layouts.app')

@section('content')
    <pagos-almacen-component :registersall="{{$remisiones}}"></pagos-almacen-component>
@endsection