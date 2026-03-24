@extends('adminlte::page')

@section('title', ' الابداع | إعدادات النظام')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Modern Timepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <style>
        .flatpickr-calendar { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; border-radius: 12px !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; border: 0 !important; }
        .flatpickr-time { height: 75px !important; }
        .flatpickr-time input { font-size: 1.5rem !important; }
    </style>
@stop

@section('content')
@livewire('admin-settings')

<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js')}}"></script>
<!-- Modern Timepicker Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Flatpickr initialization for Time
        const initPickers = () => {
            flatpickr(".timepicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "ar",
                disableMobile: "true"
            });
        };

        initPickers();

        Livewire.on('show-toast', ({ type, message }) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type || 'success',
                title: message || 'تم تنفيذ العملية بنجاح',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });
    });
</script>
@stop
