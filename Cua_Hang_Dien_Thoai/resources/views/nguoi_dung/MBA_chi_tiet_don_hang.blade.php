@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Chi tiết đơn hàng --}}
@section('title', 'Chi tiết đơn hàng ' . $order->order_code . ' - PhoneShop')

@section('content')
@auth('customer')

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account') }}" class="text-decoration-none">Tài khoản</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.orders') }}" class="text-decoration-none">Đơn hàng của tôi</a></li>
            <li class="breadcrumb-item active">{{ $order->order_code }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-receipt text-brand me-2"></i>Chi tiết đơn hàng
            <span class="text-brand">{{ $order->order_code }}</span>
        </h2>
        <a href="{{ route('account.orders') }}" class="btn btn-outline-brand">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    {{-- Thong tin don --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="small text-muted">Mã đơn hàng</div>
                    <div class="fw-bold text-brand">{{ $order->order_code }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Ngày đặt</div>
                    <div class="fw-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Trạng thái</div>
                    @switch($order->status)
                        @case('pending') <span class="badge bg-warning text-dark">Chờ xác nhận</span> @break
                        @case('confirmed') <span class="badge bg-info text-dark">Đã xác nhận</span> @break
                        @case('shipping') <span class="badge bg-primary">Đang giao</span> @break
                        @case('completed') <span class="badge bg-success">Hoàn thành</span> @break
                        @case('cancelled') <span class="badge bg-danger">Đã hủy</span> @break
                    @endswitch
                </div>
                <div class="col-md-3">
                    <div class="small text-muted">Thanh toán</div>
                    <div class="fw-semibold">
                        @switch($order->payment_method)
                            @case('cod') COD @break
                            @case('banking') Banking @break
                            @case('momo') MoMo @break
                        @endswitch
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Đã TT</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">Chưa TT</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Danh sach SP --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom fw-bold"><i class="bi bi-box-seam text-brand me-2"></i>Sản phẩm đã đặt</div>
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
                                                <div>
                                                    <div class="fw-semibold">{{ $d->product_name }}</div>
                                                    @if($d->product)
                                                        <a href="{{ route('product.show', $d->product->slug) }}" class="small text-brand text-decoration-none">Mua lại</a>
                                                    @endif
                                                </div>
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
        </div>

        {{-- Thong tin giao hang --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm position-sticky" style="top: 90px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-brand me-2"></i>Thông tin giao hàng</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted small" style="width: 100px;">Người nhận</th>
                                <td class="fw-semibold">{{ $order->shipping_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small">SĐT</th>
                                <td class="fw-semibold">{{ $order->shipping_phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small">Địa chỉ</th>
                                <td class="fw-semibold">{{ $order->shipping_address }}</td>
                            </tr>
                            @if($order->note)
                            <tr>
                                <th class="text-muted small">Ghi chú</th>
                                <td class="small">{{ $order->note }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('account.orders') }}" class="btn btn-outline-brand w-100 mt-3">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
            <a href="{{ route('home') }}" class="btn btn-brand w-100 mt-2">
                <i class="bi bi-house me-1"></i>Tiếp tục mua sắm
            </a>
            @if($order->status === 'pending')
                <form action="{{ route('account.order.cancel', $order->id) }}" method="POST" class="mt-2"
                      onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle me-1"></i>Hủy đơn hàng
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

@endauth

@guest('customer')
<div class="container py-5">
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-lock display-1 text-muted"></i>
            <h4 class="mt-3">Vui lòng đăng nhập</h4>
            <a href="{{ route('login') }}" class="btn btn-brand"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
        </div>
    </div>
</div>
@endguest

@endsection
