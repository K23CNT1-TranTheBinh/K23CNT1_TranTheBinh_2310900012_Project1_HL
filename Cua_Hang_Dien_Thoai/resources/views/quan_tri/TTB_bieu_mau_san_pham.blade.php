{{-- Tệp giao diện quản trị --}}
{{--
    bieu_mau_san_pham.blade.php — Biểu mẫu thêm/sửa sản phẩm
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $product (null khi create, SanPham khi edit)
    - $categories (collection DanhMuc)
    - $brands (collection ThuongHieu)
    - $errors (MessageBag của Laravel khi validate fail)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm')

@php
    $product = $product ?? null;
    $isEdit = (bool) $product?->id;
    // Action + method của form
    $action = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
    $imagesValue = old('images', implode("\n", $product?->images ?? []));
    if (is_array($imagesValue)) $imagesValue = implode("\n", $imagesValue);
    $specsValue = old('specs', $product?->specs ? json_encode($product->specs, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '');
    if (is_array($specsValue)) $specsValue = json_encode($specsValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-square' }} text-brand"></i>
                    {{ $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' }}
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">

                {{-- Hiển thị lỗi validate --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ $action }}" enctype="multipart/form-data">
                    @csrf
                    @if ($isEdit) @method('PUT') @endif

                    {{-- Tên sản phẩm --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $product?->name) }}" required>
                    </div>

                    {{-- Danh mục + thương hiệu --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="">— Chọn danh mục —</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}"
                                        {{ (string) old('category_id', $product?->category_id) === (string) $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <option value="">— Chọn thương hiệu —</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b->id }}"
                                        {{ (string) old('brand_id', $product?->brand_id) === (string) $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Giá + tồn kho --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Giá gốc (VND) <span class="text-danger">*</span></label>
                            <input type="number" name="price" min="0" step="1000" class="form-control"
                                   value="{{ old('price', $product?->price ?? 0) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Giá khuyến mãi (VND)</label>
                            <input type="number" name="sale_price" min="0" step="1000" class="form-control"
                                   value="{{ old('sale_price', $product?->sale_price) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tồn kho</label>
                            <input type="number" name="stock" min="0" class="form-control"
                                   value="{{ old('stock', $product?->stock ?? 0) }}">
                        </div>
                    </div>

                    {{-- Ảnh đại diện --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Ảnh đại diện</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-image"></i></span>
                            <input type="text" name="image" class="form-control"
                                   value="{{ old('image', $product?->image) }}" placeholder="URL ảnh hoặc đường dẫn cũ">
                        </div>
                        <div class="form-text">Có thể dán URL hoặc chọn tệp bên dưới. Tệp tải lên sẽ được ưu tiên.</div>
                        <input type="file" name="image_file" class="form-control mt-2"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        @if ($isEdit && $product->image_url)
                            <img src="{{ $product->image_url }}" class="mt-2 border rounded"
                                 style="max-height:90px;" alt="Ảnh hiện tại"
                                 onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                        @endif
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Ảnh thư viện</label>
                        <textarea name="images" rows="3" class="form-control"
                                  placeholder="Mỗi dòng một URL hoặc đường dẫn ảnh">{{ $imagesValue }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Thông số kỹ thuật (JSON)</label>
                        <textarea name="specs" rows="6" class="form-control font-monospace"
                                  placeholder='{"Màn hình":"6.7 inch","RAM":"8 GB"}'>{{ $specsValue }}</textarea>
                    </div>

                    {{-- Mô tả ngắn --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Mô tả ngắn</label>
                        <textarea name="short_desc" rows="2" class="form-control">{{ old('short_desc', $product?->short_desc) }}</textarea>
                    </div>

                    {{-- Mô tả chi tiết --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Mô tả chi tiết</label>
                        <textarea name="description" rows="5" class="form-control">{{ old('description', $product?->description) }}</textarea>
                    </div>

                    {{-- Switch: nổi bật + hiển thị --}}
                    <div class="mt-3 d-flex flex-wrap gap-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured"
                                {{ old('is_featured', $isEdit ? (int) $product->is_featured === 1 : false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isFeatured">Sản phẩm nổi bật</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_new" value="1" id="isNew"
                                {{ old('is_new', $isEdit ? (int) $product->is_new === 1 : true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isNew">Sản phẩm mới</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="prdStatus"
                                {{ old('status', $isEdit ? (int) $product->status === 1 : true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="prdStatus">Hiển thị sản phẩm</label>
                        </div>
                    </div>

                    {{-- Nút lưu + huỷ --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-brand text-white">
                            <i class="bi bi-save"></i> Lưu sản phẩm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
