{{-- Tệp giao diện quản trị --}}
{{--
    chi_tiet_don_hang.blade.php — Chi tiết đơn hàng
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $order: DonHang có relation details (mỗi ChiTietDonHang có product), user
    Route đổi trạng thái: PATCH admin.orders.updateStatus({id}) — body: status
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Chi tiết đơn hàng')

@php
    use App\Models\DonHang;
    $statusMap = [
        DonHang::STATUS_PENDING   => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
        DonHang::STATUS_CONFIRMED => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
        DonHang::STATUS_SHIPPING  => ['label' => 'Đang giao',   'class' => 'bg-primary'],
        DonHang::STATUS_COMPLETED => ['label' => 'Hoàn thành',   'class' => 'bg-success'],
        DonHang::STATUS_CANCELLED => ['label' => 'Đã huỷ',       'class' => 'bg-danger'],
    ];
    $paymentMethodLabels = [
        'cod' => 'Tiền mặt (COD)', 'banking' => 'Chuyển khoản',
        'momo' => 'Ví MoMo',
    ];
    $transitionMap = [
        DonHang::STATUS_PENDING => [DonHang::STATUS_CONFIRMED, DonHang::STATUS_CANCELLED],
        DonHang::STATUS_CONFIRMED => [DonHang::STATUS_SHIPPING, DonHang::STATUS_CANCELLED],
        DonHang::STATUS_SHIPPING => [DonHang::STATUS_COMPLETED],
        DonHang::STATUS_COMPLETED => [],
        DonHang::STATUS_CANCELLED => [],
    ];
    $allowedStatuses = array_merge([$order->status], $transitionMap[$order->status] ?? []);
    $info = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-secondary'];
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-receipt text-brand"></i> Đơn hàng {{ $order->order_code }}</h4>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-3">
    {{-- CỘT TRÁI: thông tin + items --}}
    <div class="col-lg-8">
        {{-- Thông tin đơn --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong><i class="bi bi-info-circle text-brand"></i> Thông tin đơn hàng</strong>
            </div>
            <div class="card-body">
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="text-muted">Mã đơn</div>
                        <div class="fw-bold fs-5">{{ $order->order_code }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Ngày đặt</div>
                        <div>{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Phương thức thanh toán</div>
                        <div>{{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Trạng thái thanh toán</div>
                        @if ($order->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Trạng thái đơn hàng hiện tại</div>
                        <span class="badge {{ $info['class'] }}">{{ $info['label'] }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Khách hàng</div>
                        <div>{{ $order->customer?->full_name ?? 'Khách vãng lai' }}</div>
                    </div>
                </div>

                {{-- Form đổi trạng thái đơn --}}
                <form method="post" action="{{ route('admin.orders.updateStatus', $order->id) }}"
                      class="mt-3 d-flex gap-2 align-items-end border-top pt-3">
                    @csrf
                    @method('PATCH')
                    <div class="flex-grow-1" style="max-width:240px;">
                        <label class="form-label small fw-semibold mb-1">Đổi trạng thái đơn</label>
                        <select name="status" class="form-select form-select-sm" {{ count($allowedStatuses) === 1 ? 'disabled' : '' }}>
                            @foreach ($allowedStatuses as $k)
                                <option value="{{ $k }}" {{ $order->status === $k ? 'selected' : '' }}>{{ $statusMap[$k]['label'] ?? $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-brand text-white" {{ count($allowedStatuses) === 1 ? 'disabled' : '' }}>
                        <i class="bi bi-check-lg"></i> Cập nhật
                    </button>
                </form>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <div class="card">
            <div class="card-header bg-white">
                <strong><i class="bi bi-box-seam text-brand"></i> Danh sách sản phẩm</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($order->details as $d)
                            <tr>
                                <td>
                                    @if ($d->product?->image)
                                        <img src="{{ $d->product->image_url }}" alt="" class="thumb" style="width:42px;height:42px;"
                                             onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width:42px;height:42px;border-radius:6px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $d->product_name }}</td>
                                <td class="text-center">{{ (int) $d->quantity }}</td>
                                <td class="text-end">{{ number_format($d->product_price, 0, ',', '.') }}₫</td>
                                <td class="text-end fw-semibold">{{ number_format($d->total_price, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Không có sản phẩm.</td></tr>
                        @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end text-muted">Tổng tiền hàng</td>
                                <td class="text-end">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted">Phí vận chuyển</td>
                                <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted">Giảm giá</td>
                                <td class="text-end text-danger">- {{ number_format($order->discount, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">TỔNG CỘNG</td>
                                <td class="text-end fw-bold fs-5 text-brand">{{ number_format($order->final_amount, 0, ',', '.') }}₫</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: thông tin giao hàng --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <strong><i class="bi bi-truck text-brand"></i> Thông tin giao hàng</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Người nhận</div>
                    <div class="fw-semibold"><i class="bi bi-person"></i> {{ $order->shipping_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Số điện thoại</div>
                    <div><i class="bi bi-telephone"></i> {{ $order->shipping_phone }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Địa chỉ giao hàng</div>
                    <div><i class="bi bi-geo-alt"></i> {{ $order->shipping_address }}</div>
                </div>
                <div class="mb-0">
                    <div class="text-muted small">Ghi chú</div>
                    <div class="fst-italic">{{ $order->note ?: '(không có)' }}</div>
                </div>
            </div>
        </div>

        @if ($order->customer)
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <strong><i class="bi bi-envelope text-brand"></i> Thông tin khách</strong>
                </div>
                <div class="card-body small">
                    <div class="mb-1"><i class="bi bi-person"></i> {{ $order->customer->full_name }}</div>
                    <div class="mb-1"><i class="bi bi-envelope"></i> {{ $order->customer->email }}</div>
                    @if (!empty($order->customer->phone))
                        <div class="mb-0"><i class="bi bi-telephone"></i> {{ $order->customer->phone }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
