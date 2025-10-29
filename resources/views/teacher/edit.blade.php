@extends('adminlte::page')

@section('title', 'تعديل المعلم')

@section('content')
    <div class="container mt-5">
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

        <div class="card shadow-lg border-0 rounded-3 ">
            <div class="card-header bg-warning text-white">
                <h5><i class="fas fa-edit"></i> تعديل بيانات المعلم</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.update', $teacher->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label>الاسم</label>
                        <input type="text" name="Name" value="{{ old('Name', $teacher->Name) }}"
                            class="form-control @error('Name') is-invalid @enderror">
                        @error('Name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>الجنس</label>
                        <select name="Sex" class="form-control @error('Sex') is-invalid @enderror">
                            <option value="ذكر" {{ $teacher->Sex == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                            <option value="أنثى" {{ $teacher->Sex == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('Sex')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-warning w-100 mr-2">
                            <i class="fas fa-save"></i> تحديث
                        </button>
                        <a href="{{ route('teacher.index') }}" type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-save"></i> إلغاء
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop
