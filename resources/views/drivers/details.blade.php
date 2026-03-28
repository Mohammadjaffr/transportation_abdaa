@extends('adminlte::page')

@section('title', ' الابداع | تفاصيل السائق')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        .driver-details-card {
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        .driver-info-item {
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }

        .driver-info-item:last-child {
            border-bottom: none;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 1.2rem;
        }
    </style>
@stop

@section('content')
    @livewire('driver-details', ['driverId' => $id])

   
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js')}}"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-toast', ({ type, message }) => {
            console.log('📦 نوع التنبيه:', type);
            console.log('📣 الرسالة:', message);

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
