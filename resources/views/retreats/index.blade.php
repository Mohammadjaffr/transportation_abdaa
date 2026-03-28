@extends('adminlte::page')

@section('title', ' الابداع | إدارة الإستثمارات')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('retreats')


@stop

