@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Đặt hàng thành công --}}
@section('title', 'Đặt hàng thành công - PhoneShop')

@section('content')
<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="card border-0 shadow-sm text-center mb-4">
                <div class="card-body p-5">
                    <div class="text-success mb-3" style="font-size: 5rem; line-height: 1;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">Đặt hàng thành công!</h2>
                    <p class="text-muted mb-0">Cảm ơn bạn đã đặt hàng tại PhoneShop. Chúng tôi sẽ liên hệ để xác nhận đơn hàng sớm nhất.</p>
                </div>
            </div>

            {{-- Thong tin don --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-brand me-2"></i>Thông tin đơn hàng</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Mã đơn hàng</div>
                            <div class="fw-bold text-brand fs-5">{{ $order->order_code }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Ngày đặt</div>
                            <div class="fw-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Trạng thái</div>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-info text-dark">Đã xác nhận</span>
                                    @break
                                @case('shipping')
                                    <span class="badge bg-primary">Đang giao</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Phương thức thanh toán</div>
                            <div class="fw-semibold">
                                @switch($order->payment_method)
                                    @case('cod') Tiền mặt (COD) @break
                                    @case('banking') Chuyển khoản @break
                                    @case('momo') Ví MoMo @break
                                @endswitch
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">Chưa thanh toán</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danh sach SP --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom fw-bold"><i class="bi bi-box-seam text-brand me-2"></i>Danh sách sản phẩm</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end pe-3">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subtotal = 0; @endphp
                                @foreach($order->details as $d)
                                    @php $subtotal += $d->total_price; @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($d->product)
                                                    <img src="{{ $d->product->image_url }}" alt="{{ $d->product_name }}"
                                                         style="width: 48px; height: 48px; object-fit: contain; background: #f8f9fa; border-radius: 4px;"
                                                         onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                                @endif
                                                <span class="fw-semibold">{{ $d->product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $d->quantity }}</td>
                                        <td class="text-end">{{ number_format($d->product_price, 0, ',', '.') }}₫</td>
                                        <td class="text-end fw-bold pe-3">{{ number_format($d->total_price, 0, ',', '.') }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end text-muted">Tạm tính</td>
                                    <td class="text-end fw-semibold pe-3">{{ number_format($subtotal, 0, ',', '.') }}₫</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end text-muted">Phí vận chuyển</td>
                                    <td class="text-end fw-semibold pe-3">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-muted">Giảm giá</td>
                                    <td class="text-end fw-semibold text-danger pe-3">-{{ number_format($order->discount, 0, ',', '.') }}₫</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng cộng</td>
                                    <td class="text-end fw-bold text-brand fs-5 pe-3">{{ number_format($order->final_amount, 0, ',', '.') }}₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Thong tin giao hang --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-brand me-2"></i>Thông tin giao hàng</h6>
                    <div class="row g-2 small">
                        <div class="col-md-4"><span class="text-muted">Người nhận:</span> <span class="fw-semibold">{{ $order->shipping_name }}</span></div>
                        <div class="col-md-4"><span class="text-muted">SĐT:</span> <span class="fw-semibold">{{ $order->shipping_phone }}</span></div>
                        <div class="col-md-4"><span class="text-muted">Địa chỉ:</span> <span class="fw-semibold">{{ $order->shipping_address }}</span></div>
                        @if($order->note)
                        <div class="col-12"><span class="text-muted">Ghi chú:</span> {{ $order->note }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('home') }}" class="btn btn-outline-brand">
                    <i class="bi bi-house me-1"></i>Tiếp tục mua sắm
                </a>
                <a href="{{ route('account.orders') }}" class="btn btn-brand">
                    <i class="bi bi-bag-check me-1"></i>Xem đơn hàng
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
