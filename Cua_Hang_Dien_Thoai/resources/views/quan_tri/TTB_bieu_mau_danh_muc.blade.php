{{-- Tệp giao diện quản trị --}}
{{--
    bieu_mau_danh_muc.blade.php — Biểu mẫu thêm/sửa danh mục
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $category (null khi create, DanhMuc khi edit)
    - $errors (MessageBag)
    Fields: name, description (status sẽ mặc định nếu controller không có)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', isset($category) ? 'Sửa danh mục' : 'Thêm danh mục')

@php
    $category = $category ?? null;
    $isEdit = (bool) $category?->id;
    $action = $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store');
@endphp

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-square' }} text-brand"></i>
                    {{ $isEdit ? 'Sửa danh mục' : 'Thêm danh mục mới' }}
                </h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               value="{{ old('name', $category?->name) }}" placeholder="VD: Điện thoại cao cấp">
                        <div class="form-text">Slug sẽ được tạo tự động.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $category?->description) }}</textarea>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="catStatus"
                            {{ old('status', $isEdit ? (int) $category->status === 1 : true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="catStatus">Hiển thị danh mục</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand text-white">
                            <i class="bi bi-save"></i> {{ $isEdit ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
