@extends('adminlte::page')

@section('title', ' الابداع | إدارة التحضير السائقين')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('preparation-drivers')

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

