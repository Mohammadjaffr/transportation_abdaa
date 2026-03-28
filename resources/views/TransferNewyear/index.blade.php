@extends('adminlte::page')

@section('title', ' الابداع | نقل سنة جديده ')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('transfer-newyear')

@stop

