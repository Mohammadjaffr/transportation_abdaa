@extends('adminlte::page')

@section('title', ' الابداع | إدارة المناطق')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('regions')


@stop

