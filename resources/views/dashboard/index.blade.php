@extends('adminlte::page')

@section('title', 'الابداع | لوحة التحكم')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('dashboard')


@stop

