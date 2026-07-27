{{-- Tệp giao diện quản trị --}}
{{--
    bieu_mau_ma_giam_gia.blade.php — Biểu mẫu thêm/sửa mã giảm giá
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $coupon (null khi create, MaGiamGia khi edit)
    - $errors (MessageBag)
    Fields: code, discount_type(select percent/fixed), discount_value, min_order_amount,
            start_date(datetime-local), end_date, usage_limit
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', isset($coupon) ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá')

@php
    $coupon = $coupon ?? null;
    $isEdit = (bool) $coupon?->id;
    $action = $isEdit ? route('admin.coupons.update', $coupon) : route('admin.coupons.store');

    // Format datetime-local: Y-m-d\TH:i
    $fmtDt = function ($d) {
        if (!$d) return '';
        try { return \Carbon\Carbon::parse($d)->format('Y-m-d\TH:i'); }
        catch (\Throwable $e) { return ''; }
    };
@endphp

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-square' }} text-brand"></i>
                    {{ $isEdit ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá mới' }}
                </h5>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">
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

                    {{-- Mã coupon --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" required
                               value="{{ old('code', $coupon?->code) }}"
                               placeholder="VD: SUMMER2026"
                               style="text-transform:uppercase;">
                    </div>

                    {{-- Loại + giá trị --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Loại</label>
                            <select name="discount_type" class="form-select" id="discountType">
                                <option value="percent" {{ old('discount_type', $coupon?->discount_type) === 'percent' ? 'selected' : '' }}>Giảm %</option>
                                <option value="fixed"   {{ old('discount_type', $coupon?->discount_type) === 'fixed'   ? 'selected' : '' }}>Giảm số tiền</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Giá trị <span id="valSuffix">(%)</span></label>
                            <input type="number" name="discount_value" id="discountValue" min="1" step="1" class="form-control" required
                                   value="{{ old('discount_value', $coupon?->discount_value) }}">
                        </div>
                    </div>

                    {{-- Đơn tối thiểu --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Đơn hàng tối thiểu (VND)</label>
                        <input type="number" name="min_order_amount" min="0" step="1000" class="form-control"
                               value="{{ old('min_order_amount', $coupon?->min_order_amount ?? 0) }}">
                    </div>

                    {{-- Ngày bắt đầu + kết thúc (datetime-local) --}}
                    <div class="row g-2 mt-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bắt đầu</label>
                            <input type="datetime-local" name="start_date" class="form-control" required
                                   value="{{ old('start_date', $fmtDt($coupon?->start_date ?? now())) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Kết thúc</label>
                            <input type="datetime-local" name="end_date" class="form-control" required
                                   value="{{ old('end_date', $fmtDt($coupon?->end_date ?? now()->addDays(30))) }}">
                        </div>
                    </div>

                    {{-- Số lần dùng tối đa --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Số lần sử dụng tối đa</label>
                        <input type="number" name="usage_limit" min="1" class="form-control" required
                               value="{{ old('usage_limit', $coupon?->usage_limit ?? 1) }}">
                    </div>

                    {{-- Switch trạng thái --}}
                    <div class="form-check form-switch mt-3">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="cpnStatus"
                            {{ old('status', $isEdit ? (int) $coupon->status === 1 : true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cpnStatus">Hoạt động</label>
                    </div>

                    {{-- Nút lưu + huỷ --}}
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-brand text-white">
                            <i class="bi bi-save"></i> {{ $isEdit ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Đổi suffix (%) hoặc (VND) khi đổi loại giảm giá
    const discountType = document.getElementById('discountType');
    const discountValue = document.getElementById('discountValue');
    function syncDiscountInput() {
        const isPercent = discountType.value === 'percent';
        document.getElementById('valSuffix').textContent = isPercent ? '(%)' : '(VND)';
        discountValue.step = isPercent ? '1' : '1000';
        discountValue.max = isPercent ? '100' : '';
    }
    discountType.addEventListener('change', syncDiscountInput);
    syncDiscountInput();
</script>
@endpush
