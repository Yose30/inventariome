@extends('layouts.app')

@section('content')
    <pagos-almacen-component :registersall="{{$remisiones}}" :listresponsables="{{$responsables}}"></pagos-almacen-component>
@endsection