@extends('adminlte::page')

@section('title', ' الابداع | إدارة الحافلات')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('buses')


@stop

