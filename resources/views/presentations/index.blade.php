@extends('adminlte::page')

@section('title', ' الابداع | إدارة الحضور')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('presentations')

@stop

