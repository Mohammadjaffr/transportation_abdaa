@extends('adminlte::page')

@section('title', 'الإبداع | إدارة الواتساب')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
    @livewire('whatsapp-management')
@stop