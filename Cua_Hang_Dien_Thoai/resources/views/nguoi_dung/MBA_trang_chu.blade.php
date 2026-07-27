@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Trang chủ --}}
@section('title', 'PhoneShop - Cửa hàng điện thoại chính hãng')

@section('content')

{{-- Hero --}}
<section class="hero-gradient text-white py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-light text-brand mb-3">★ Đồ án Nhom 5 - PhoneShop</span>
                <h1 class="display-4 fw-bold mb-3">Điện thoại chính hãng<br>giá tốt nhất Việt Nam</h1>
                <p class="lead mb-4">Khám phá bộ sưu tập smartphone mới nhất từ Apple, Samsung, Xiaomi, OPPO, Vivo, Nokia. Bảo hành chính hãng, giao hàng miễn phí.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('catalog') }}" class="btn btn-light btn-lg fw-semibold text-brand">
                        <i class="bi bi-bag me-2"></i>Mua sắm ngay
                    </a>
                    <a href="{{ route('catalog.category', 'dien-thoai-cao-cap') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-stars me-2"></i>Flagship
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                @if(!empty($featured) && isset($featured[0]))
                    <img src="{{ $featured[0]->image_url }}" alt="{{ $featured[0]->name }}"
                         class="img-fluid" style="max-height: 360px; object-fit: contain;"
                         onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                @else
                    <img src="{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}" alt="PhoneShop" class="img-fluid" style="max-height: 360px;">
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Danh muc --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2">Danh mục sản phẩm</h2>
            <p class="text-muted">Chọn danh mục phù hợp nhu cầu của bạn</p>
        </div>
        <div class="row g-3">
            @foreach($categories as $c)
                <div class="col-md-4">
                    <a href="{{ route('catalog.category', $c->slug) }}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark product-card">
                        <div class="card-body p-4 text-center">
                            <div class="display-5 text-brand mb-3">
                                @if($c->slug == 'dien-thoai-cao-cap')
                                    <i class="bi bi-stars"></i>
                                @elseif($c->slug == 'dien-thoai-tam-trung')
                                    <i class="bi bi-phone"></i>
                                @else
                                    <i class="bi bi-phone-fill"></i>
                                @endif
                            </div>
                            <h5 class="fw-bold">{{ $c->name }}</h5>
                            <p class="small text-muted mb-0">{{ $c->description }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SP noi bat --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-1">Sản phẩm nổi bật</h2>
                <p class="text-muted mb-0">Những siêu phẩm được yêu thích nhất</p>
            </div>
            <a href="{{ route('catalog') }}" class="btn btn-outline-brand">Xem tất cả <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($featured as $p)
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 product-card">
                        <a href="{{ route('product.show', $p->slug) }}" class="text-decoration-none text-dark">
                            <img src="{{ $p->image_url }}" class="card-img-top" alt="{{ $p->name }}" style="height:220px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2">{{ $p->name }}</h6>
                                <p class="small text-muted mb-2">{{ Str::limit($p->short_desc ?? $p->description, 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-brand">{{ number_format($p->current_price, 0, ',', '.') }}đ</div>
                                        @if($p->sale_price && $p->sale_price < $p->price)
                                            <div class="price-strike">{{ number_format($p->price, 0, ',', '.') }}đ</div>
                                        @endif
                                    </div>
                                    <span class="badge bg-brand">Mua</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Banner KM --}}
<section class="py-5">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-4 rounded-4 text-white" style="background: linear-gradient(135deg, #1f2937, #374151); min-height: 160px;">
                    <div class="row align-items-center h-100">
                        <div class="col-8">
                            <span class="badge bg-brand mb-2">APPLE</span>
                            <h4 class="fw-bold">iPhone 15 Pro Max</h4>
                            <p class="small mb-2">Titan cao cấp - Chip A17 Pro</p>
                            <a href="{{ route('catalog.brand', 'apple') }}" class="btn btn-brand btn-sm">Mua ngay</a>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-phone display-4 text-brand"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-4 text-white" style="background: linear-gradient(135deg, #0d9488, #14b8a6); min-height: 160px;">
                    <div class="row align-items-center h-100">
                        <div class="col-8">
                            <span class="badge bg-light text-teal mb-2">SAMSUNG</span>
                            <h4 class="fw-bold">Galaxy S24 Ultra</h4>
                            <p class="small mb-2">AI tích hợp - Camera 200MP</p>
                            <a href="{{ route('catalog.brand', 'samsung') }}" class="btn btn-light btn-sm text-teal">Mua ngay</a>
                        </div>
                        <div class="col-4 text-center">
                            <i class="bi bi-phone display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Thuong hieu --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Thương hiệu</h2>
        </div>
        <div class="row row-cols-3 row-cols-md-6 g-3 text-center">
            @foreach($brands as $b)
                <div class="col">
                    <a href="{{ route('catalog.brand', $b->slug) }}" class="d-block p-3 border rounded-3 text-decoration-none text-dark product-card">
                        <div class="fw-bold text-brand fs-5">{{ $b->name }}</div>
                        <div class="small text-muted">{{ $b->slug }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SP moi --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-1">Sản phẩm mới</h2>
                <p class="text-muted mb-0">Mới cập nhật kho</p>
            </div>
            <a href="{{ route('catalog') }}" class="btn btn-outline-brand">Xem tất cả <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            @foreach($newest as $p)
                <div class="col">
                    <div class="card border-0 shadow-sm h-100 product-card">
                        <a href="{{ route('product.show', $p->slug) }}" class="text-decoration-none text-dark">
                            <img src="{{ $p->image_url }}" class="card-img-top" alt="{{ $p->name }}" style="height:220px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2">{{ $p->name }}</h6>
                                <p class="small text-muted mb-2">{{ Str::limit($p->short_desc ?? $p->description, 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-brand">{{ number_format($p->current_price, 0, ',', '.') }}đ</div>
                                        @if($p->sale_price && $p->sale_price < $p->price)
                                            <div class="price-strike">{{ number_format($p->price, 0, ',', '.') }}đ</div>
                                        @endif
                                    </div>
                                    <span class="badge bg-brand">Mua</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
