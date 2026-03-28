@extends('adminlte::page')

@section('title', ' الابداع | إدارة المستخدمين')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('register-user')

@stop

