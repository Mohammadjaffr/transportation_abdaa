@extends('adminlte::page')

@section('title', ' الابداع | إدارة الطلاب')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('students')
@stop

