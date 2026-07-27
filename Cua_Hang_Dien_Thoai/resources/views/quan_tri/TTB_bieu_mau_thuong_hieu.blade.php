{{-- Tệp giao diện quản trị --}}
{{--
    bieu_mau_thuong_hieu.blade.php — Biểu mẫu thêm/sửa thương hiệu
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $brand (null khi create, ThuongHieu khi edit)
    - $errors (MessageBag)
    Fields: name (slug tự sinh trong controller)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', isset($brand) ? 'Sửa thương hiệu' : 'Thêm thương hiệu')

@php
    $brand = $brand ?? null;
    $isEdit = (bool) $brand?->id;
    $action = $isEdit ? route('admin.brands.update', $brand) : route('admin.brands.store');
@endphp

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-square' }} text-brand"></i>
                    {{ $isEdit ? 'Sửa thương hiệu' : 'Thêm thương hiệu mới' }}
                </h5>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ $action }}">
                    @csrf
                    @if ($isEdit) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               value="{{ old('name', $brand?->name) }}" placeholder="VD: Apple">
                        <div class="form-text">Slug sẽ được tạo tự động.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Logo (URL hoặc tên tệp)</label>
                        <input type="text" name="logo" class="form-control"
                               value="{{ old('logo', $brand?->logo) }}" placeholder="VD: apple-logo.png">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="brandStatus"
                            {{ old('status', $isEdit ? (int) $brand->status === 1 : true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="brandStatus">Hiển thị thương hiệu</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand text-white">
                            <i class="bi bi-save"></i> {{ $isEdit ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
