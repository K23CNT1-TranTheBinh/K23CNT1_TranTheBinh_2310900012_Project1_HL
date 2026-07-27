@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Thanh toán --}}
@section('title', 'Thanh toán - PhoneShop')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart') }}" class="text-decoration-none">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4"><i class="bi bi-credit-card text-brand me-2"></i>Thanh toán</h2>

    @auth('customer')
        @if(empty($items) || count($items) == 0)
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-bag-x display-1 text-muted"></i>
                    <h4 class="mt-3">Giỏ hàng trống</h4>
                    <p class="text-muted">Không có sản phẩm để thanh toán.</p>
                    <a href="{{ route('catalog') }}" class="btn btn-brand">Mua sắm ngay</a>
                </div>
            </div>
        @else
            <form action="{{ route('checkout.place') }}" method="POST">
                @csrf
                <div class="row g-4">
                    {{-- Form giao hang --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="bi bi-truck text-brand me-2"></i>Thông tin giao hàng</h5>
                                @php $u = auth('customer')->user(); @endphp
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_name" class="form-control" required
                                               value="{{ old('shipping_name', $u->full_name) }}" placeholder="Nguyễn Văn A">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_phone" class="form-control" required pattern="(?:0|\+84)\d{9,10}"
                                               value="{{ old('shipping_phone', $u->phone) }}" placeholder="0912345678">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                        <textarea name="shipping_address" class="form-control" rows="2" required placeholder="Số nhà, đường, phường, quận, TP">{{ old('shipping_address', $u->address) }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Ghi chú (tuỳ chọn)</label>
                                        <textarea name="note" class="form-control" rows="2" placeholder="Yêu cầu giao hàng...">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Phuong thuc thanh toan --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="bi bi-wallet2 text-brand me-2"></i>Phương thức thanh toán</h5>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2 cursor-pointer" style="cursor: pointer;">
                                            <input type="radio" name="payment_method" value="cod" class="form-check-input" checked required>
                                            <div>
                                                <div class="fw-semibold"><i class="bi bi-cash-coin text-brand me-1"></i> COD</div>
                                                <div class="small text-muted">Thanh toán khi nhận</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2" style="cursor: pointer;">
                                            <input type="radio" name="payment_method" value="banking" class="form-check-input" required>
                                            <div>
                                                <div class="fw-semibold"><i class="bi bi-bank2 text-brand me-1"></i> Banking</div>
                                                <div class="small text-muted">Chuyển khoản</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="border rounded-3 p-3 d-flex align-items-center gap-2" style="cursor: pointer;">
                                            <input type="radio" name="payment_method" value="momo" class="form-check-input" required>
                                            <div>
                                                <div class="fw-semibold"><i class="bi bi-wallet text-brand me-1"></i> MoMo</div>
                                                <div class="small text-muted">Ví điện tử</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ma giam gia --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated text-brand me-2"></i>Mã giảm giá</h5>
                                <div class="input-group">
                                    <input type="text" name="coupon_code" class="form-control" value="{{ old('coupon_code') }}" placeholder="Nhập mã (vd: SALE10P, WELCOME20)">
                                    <button type="submit" class="btn btn-outline-brand" formaction="{{ route('checkout') }}" formmethod="POST">Áp dụng</button>
                                </div>
                                <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Mã sẽ được áp dụng khi xác nhận đơn hàng.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm position-sticky" style="top: 90px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Đơn hàng của bạn</h5>
                                <div class="list-group list-group-flush mb-3 scrollbar-thin" style="max-height: 320px; overflow-y: auto;">
                                    @php $subtotal = 0; @endphp
                                    @foreach($items as $item)
                                        @php
                                            $price = $item->product->current_price;
                                            $lineTotal = $price * $item->quantity;
                                            $subtotal += $lineTotal;
                                        @endphp
                                        <div class="list-group-item d-flex align-items-center gap-2 px-0">
                                            <div class="position-relative">
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                                     style="width: 50px; height: 50px; object-fit: contain; background: #f8f9fa; border-radius: 4px;"
                                                     onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                                <span class="badge bg-brand position-absolute top-0 start-100 translate-middle">{{ $item->quantity }}</span>
                                            </div>
                                            <div class="flex-grow-1 small">
                                                <div class="fw-semibold text-truncate" style="max-width: 160px;">{{ $item->product->name }}</div>
                                                <div class="text-brand">{{ number_format($lineTotal, 0, ',', '.') }}₫</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính</span>
                                    <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí vận chuyển</span>
                                    <span class="fw-semibold">{{ number_format($shippingFee ?? 30000, 0, ',', '.') }}₫</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Tổng cộng</span>
                                    <span class="fw-bold text-brand fs-5">{{ number_format($subtotal + ($shippingFee ?? 30000), 0, ',', '.') }}₫</span>
                                </div>
                                <button type="submit" class="btn btn-brand w-100 mb-2">
                                    <i class="bi bi-bag-check me-1"></i>Đặt hàng
                                </button>
                                <a href="{{ route('cart') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-left me-1"></i>Quay lại giỏ hàng
                                </a>
                                <div class="small text-muted text-center mt-3">
                                    <i class="bi bi-shield-check me-1"></i>Bằng việc đặt hàng bạn đồng ý với điều khoản của PhoneShop
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-lock display-1 text-muted"></i>
                <h4 class="mt-3">Vui lòng đăng nhập</h4>
                <p class="text-muted">Bạn cần đăng nhập để tiến hành thanh toán.</p>
                <a href="{{ route('login') }}" class="btn btn-brand"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
            </div>
        </div>
    @endauth

</div>
@endsection
