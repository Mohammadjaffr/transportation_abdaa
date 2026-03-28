@extends('adminlte::page')

@section('title', ' الابداع | إدارة الرواتب')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
@stop

@section('content')
@livewire('wages')

<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js')}}"></script>
<script>
  document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-toast', (event) => {
            // Livewire 3 يضع البيانات داخل العنصر الأول من المصفوفة
            const data = event[0]; 

            console.log('📦 نوع التنبيه:', data.type);
            console.log('📣 الرسالة:', data.message);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.type || 'success',
                title: data.message || 'تم تنفيذ العملية بنجاح', // الآن سيقرأ رسالة الكنترول بنجاح!
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

