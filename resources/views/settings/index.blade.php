@extends('adminlte::page')

@section('title', 'الابداع | إعدادات النظام')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    @livewire('admin-settings')
@stop