{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_ma_giam_gia.blade.php — Danh sách mã giảm giá
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $coupons: collection MaGiamGia (field: code, discount_type, discount_value, min_order_amount,
                start_date, end_date, usage_limit, used_count, status)
    Route resource (except show): admin.coupons.index/create/store/edit/update/destroy
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Mã giảm giá')

@section('content')

{{-- Thanh công cụ --}}
<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-ticket-perforated text-brand"></i> Danh sách mã giảm giá</strong>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-brand text-white">
            <i class="bi bi-plus-lg"></i> Thêm mã giảm giá
        </a>
    </div>
</div>

{{-- Bảng mã giảm giá --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th class="text-end">Giá trị</th>
                        <th class="text-end">Đơn tối thiểu</th>
                        <th>Thời gian</th>
                        <th style="min-width:140px;">Lượt dùng</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($coupons as $c)
                    @php
                        $used  = (int) $c->used_count;
                        $limit = max((int) $c->usage_limit, 1);
                        $pct   = min(100, round($used / $limit * 100));
                        $expired = strtotime($c->end_date) < time();
                    @endphp
                    <tr>
                        <td><span class="badge bg-brand fs-6">{{ $c->code }}</span></td>
                        <td>
                            @if ($c->discount_type === 'percent')
                                <span class="badge bg-info">Giảm %</span>
                            @else
                                <span class="badge bg-primary">Số tiền</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">
                            @if ($c->discount_type === 'percent')
                                {{ (float) $c->discount_value }}%
                            @else
                                {{ number_format($c->discount_value, 0, ',', '.') }}₫
                            @endif
                        </td>
                        <td class="text-end small">{{ number_format($c->min_order_amount, 0, ',', '.') }}₫</td>
                        <td class="small text-muted">
                            <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($c->start_date)->format('d/m/Y') }}<br>
                            <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::parse($c->end_date)->format('d/m/Y') }}
                            @if ($expired)
                                <span class="badge bg-secondary ms-1">Hết hạn</span>
                            @endif
                        </td>
                        <td>
                            {{-- Progress bar lượt dùng used/limit --}}
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar bg-brand" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="small text-muted nowrap">{{ $used }}/{{ $limit }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if ((int) $c->status === 1 && !$expired)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Tắt</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.coupons.edit', $c->id) }}" class="btn btn-sm btn-outline-brand" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="{{ route('admin.coupons.destroy', $c->id) }}" class="d-inline"
                                  onsubmit="return confirm('Xoá mã giảm giá này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Chưa có mã giảm giá nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
