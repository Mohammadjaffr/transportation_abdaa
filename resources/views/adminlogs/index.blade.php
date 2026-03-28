@extends('adminlte::page')

@section('title', 'وجهة | تتبع حركة النظام')



@section('css')
    <link rel="stylesheet" href="{{ asset('css/badge.css') }}">
    
@stop

@section('content')
@livewire('adminlogs')


@stop
