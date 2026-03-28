@extends('adminlte::page')

@section('title', ' الابداع | إدارة السائقين')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('drivers')
@stop

