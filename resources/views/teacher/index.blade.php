@extends('adminlte::page')

@section('title', 'الابداع | إدارة المعلمين')

@section('content')
<div class="container mt-4">
@if (session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: "{{ session('error') ? 'error' : 'success' }}",
                title: "{{ session('error') ?? session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });
    </script>
@endif

    {{-- تنبيهات النجاح --}}
    @if(session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif

    {{-- نموذج إضافة معلم --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-user-plus"></i> إضافة معلم جديد</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('teacher.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label>الاسم</label>
                    <input type="text" name="Name" class="form-control @error('Name') is-invalid @enderror"
                           placeholder="أدخل الاسم الكامل" value="{{ old('Name') }}">
                    @error('Name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-3">
                    <label>الجنس</label>
                    <select name="Sex" class="form-control @error('Sex') is-invalid @enderror">
                        <option value="">اختر الجنس</option>
                        <option value="ذكر" {{ old('Sex') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                        <option value="أنثى" {{ old('Sex') == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('Sex') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-user-check"></i> إضافة
                </button>
            </form>
        </div>
    </div>

    {{-- جدول عرض المعلمين --}}
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-users"></i> قائمة المعلمين</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered text-center align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الجنس</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>{{ $teacher->Name }}</td>
                            <td>{{ $teacher->Sex }}</td>
                            <td>
                                <a href="{{ route('teacher.edit', $teacher->id) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>

                                <form action="{{ route('teacher.destroy', $teacher->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirmDelete(event, '{{ $teacher->Name }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">لا يوجد معلمين بعد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- سكريبت الحذف بـ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(e, name) {
        e.preventDefault();
        Swal.fire({
            title: 'تأكيد الحذف',
            text: `هل أنت متأكد من حذف "${name}"؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
</script>
@stop
