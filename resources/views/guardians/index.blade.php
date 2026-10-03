@extends('adminlte::page')

@section('title', 'الابداع | إدارة أولياء الأمور')

@section('css')

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

@stop


@section('content')

    @livewire('guardians')

@stop
