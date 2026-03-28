@extends('adminlte::page')

@section('title', ' الابداع | إدارة تحضير الطلاب')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('preparation-stus')


@stop

