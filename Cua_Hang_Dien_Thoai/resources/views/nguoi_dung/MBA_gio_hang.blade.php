@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Giỏ hàng --}}
@section('title', 'Giỏ hàng - PhoneShop')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4"><i class="bi bi-bag text-brand me-2"></i>Giỏ hàng của bạn</h2>

    @auth('customer')
        @if(empty($items) || count($items) == 0)
            {{-- Empty --}}
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-bag-x display-1 text-muted"></i>
                    <h4 class="mt-3">Giỏ hàng trống</h4>
                    <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá cửa hàng của chúng tôi!</p>
                    <a href="{{ route('catalog') }}" class="btn btn-brand"><i class="bi bi-bag me-1"></i>Tiếp tục mua sắm</a>
                </div>
            </div>
        @else
            <div class="row g-4">
                {{-- List --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Sản phẩm</th>
                                            <th class="text-center">Đơn giá</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Thành tiền</th>
                                            <th class="text-center pe-3">Xoá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subtotal = 0; @endphp
                                        @foreach($items as $item)
                                            @php
                                                $price = $item->product->current_price;
                                                $lineTotal = $price * $item->quantity;
                                                $subtotal += $lineTotal;
                                            @endphp
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                                             style="width: 64px; height: 64px; object-fit: contain; background: #f8f9fa; border-radius: 6px;"
                                                             onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                                        <div>
                                                            <a href="{{ route('product.show', $item->product->slug) }}" class="text-decoration-none text-dark fw-semibold">
                                                                {{ $item->product->name }}
                                                            </a>
                                                            <div class="small text-muted">{{ $item->product->brand->name ?? '' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-brand fw-semibold">{{ number_format($price, 0, ',', '.') }}₫</div>
                                                    @if($item->product->discount_percent > 0)
                                                        <div class="price-strike">{{ number_format($item->product->price, 0, ',', '.') }}₫</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex justify-content-center">
                                                        @csrf
                                                        <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                                        <div class="input-group input-group-sm" style="width: 120px;">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="this.form.quantity.value = Math.max(1, parseInt(this.form.quantity.value)-1); this.form.submit()"><i class="bi bi-dash"></i></button>
                                                            <input type="number" name="quantity" class="form-control text-center" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" onchange="this.form.submit()">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="this.form.quantity.value = Math.min({{ $item->product->stock }}, parseInt(this.form.quantity.value)+1); this.form.submit()"><i class="bi bi-plus"></i></button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="text-end fw-bold">{{ number_format($lineTotal, 0, ',', '.') }}₫</td>
                                                <td class="text-center pe-3">
                                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Xoá sản phẩm này khỏi giỏ hàng?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('catalog') }}" class="btn btn-outline-brand"><i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm</a>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm position-sticky" style="top: 90px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính</span>
                                <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="fw-semibold">30.000₫</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Tổng cộng</span>
                                <span class="fw-bold text-brand fs-5">{{ number_format($subtotal + 30000, 0, ',', '.') }}₫</span>
                            </div>
                            <a href="{{ route('checkout') }}" class="btn btn-brand w-100 mb-2">
                                <i class="bi bi-credit-card me-1"></i>Tiến hành thanh toán
                            </a>
                            <div class="small text-muted text-center mt-2">
                                <i class="bi bi-shield-lock me-1"></i>Thanh toán an toàn & bảo mật
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-lock display-1 text-muted"></i>
                <h4 class="mt-3">Vui lòng đăng nhập</h4>
                <p class="text-muted">Bạn cần đăng nhập để xem giỏ hàng của mình.</p>
                <a href="{{ route('login') }}" class="btn btn-brand"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-outline-brand">Đăng ký</a>
            </div>
        </div>
    @endauth

</div>
@endsection
