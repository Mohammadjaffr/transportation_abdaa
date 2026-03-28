@extends('adminlte::page')

@section('title', ' الابداع | إدارة التحضير السائقين')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('preparation-drivers')


@stop

