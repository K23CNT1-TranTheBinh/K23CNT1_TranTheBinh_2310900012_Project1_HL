{{-- Bố cục chung cho khu vực người dùng PhoneShop --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PhoneShop - Cửa hàng điện thoại chính hãng')</title>
    <meta name="description" content="PhoneShop - Cửa hàng điện thoại chính hãng giá tốt nhất Việt Nam">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}">

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root { --brand: #f97316; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            color: #1f2937;
        }
        .btn-brand { background: var(--brand); color: #fff; border-color: var(--brand); }
        .btn-brand:hover { background: #ea580c; color: #fff; border-color: #ea580c; }
        .btn-outline-brand { background: #fff; color: var(--brand); border-color: var(--brand); }
        .btn-outline-brand:hover { background: var(--brand); color: #fff; }
        .text-brand { color: var(--brand) !important; }
        .bg-brand { background: var(--brand) !important; }
        .border-brand { border-color: var(--brand) !important; }
        .header-sticky {
            position: sticky; top: 0; z-index: 1030;
            background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .nav-link-brand { color: #1f2937; font-weight: 500; }
        .nav-link-brand:hover, .nav-link-brand.active { color: var(--brand); }
        .hero-gradient {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 60%, #c2410c 100%);
        }
        .line-clamp-2 {
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; min-height: 2.8em;
        }
        .product-card { transition: transform .2s ease, box-shadow .2s ease; height: 100%; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .avatar-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--brand); color: #fff; font-weight: 700;
        }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .footer-brand { background: #1f2937; color: #d1d5db; }
        .footer-brand a { color: #d1d5db; text-decoration: none; }
        .footer-brand a:hover { color: var(--brand); }
        .qty-input { width: 60px; text-align: center; }
        .price-strike { text-decoration: line-through; color: #9ca3af; font-size: 0.9rem; }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex flex-column min-vh-100">

    {{-- Top strip --}}
    <div class="bg-dark text-white py-1 small">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-telephone-fill text-brand me-1"></i> Hotline: 1900 1234</span>
            <span class="d-none d-md-inline"><i class="bi bi-truck text-brand me-1"></i> Miễn phí giao hàng từ 500.000đ</span>
        </div>
    </div>

    {{-- Header --}}
    <header class="header-sticky">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container">
                {{-- Logo --}}
                <a class="navbar-brand d-flex align-items-center fw-bold fs-4" href="{{ route('home') }}">
                    <i class="bi bi-phone-fill text-brand me-2 fs-3"></i>
                    <span>Phone<span class="text-brand">Shop</span></span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navMain">
                    {{-- Search --}}
                    <form class="d-flex mx-lg-auto my-2 my-lg-0" action="{{ route('catalog.search') }}" method="GET" style="min-width: 260px; max-width: 420px; flex: 1;">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Tìm điện thoại..." value="{{ request('q') }}">
                            <button class="btn btn-brand" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    {{-- Nav --}}
                    <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link nav-link-brand {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-brand" href="{{ route('catalog') }}">Điện thoại</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-brand" href="{{ route('catalog.category', 'dien-thoai-cao-cap') }}">Cao cấp</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-brand" href="{{ route('catalog.category', 'dien-thoai-tam-trung') }}">Tầm trung</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-brand" href="{{ route('catalog.category', 'dien-thoai-gia-re') }}">Giá rẻ</a>
                        </li>

                        {{-- GioHang --}}
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-brand position-relative" href="{{ route('cart') }}">
                                <i class="bi bi-bag"></i>
                                @auth('customer')
                                    @php $count = auth('customer')->user()->carts->count(); @endphp
                                    @if($count > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $count }}</span>
                                    @endif
                                @endauth
                            </a>
                        </li>

                        {{-- Account --}}
                        <li class="nav-item dropdown ms-lg-1">
                            <a class="btn btn-link nav-link-brand dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-4"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @auth('customer')
                                    <li><span class="dropdown-item-text small text-muted">Xin chào,</span></li>
                                    <li><h6 class="dropdown-header text-brand">{{ auth('customer')->user()->full_name }}</h6></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('account') }}"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                                    <li><a class="dropdown-item" href="{{ route('account.orders') }}"><i class="bi bi-bag-check me-2"></i>Đơn hàng</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                        </form>
                                    </li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập</a></li>
                                    <li><a class="dropdown-item" href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i>Đăng ký</a></li>
                                @endauth
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    {{-- Flash --}}
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Main --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer-brand mt-auto pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3"><i class="bi bi-phone-fill text-brand me-2"></i>Phone<span class="text-brand">Shop</span></h5>
                    <p class="small">Cửa hàng điện thoại chính hãng giá tốt nhất Việt Nam. Cam kết hàng mới 100%, bảo hành chính hãng.</p>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><a href="#">Chính sách đổi trả</a></li>
                        <li class="mb-1"><a href="#">Chính sách bảo hành</a></li>
                        <li class="mb-1"><a href="#">Phương thức thanh toán</a></li>
                        <li class="mb-1"><a href="#">Vận chuyển</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold mb-3">Liên hệ</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-1"><i class="bi bi-geo-alt me-1"></i>123 Lê Lợi, Q.1, TP.HCM</li>
                        <li class="mb-1"><i class="bi bi-telephone me-1"></i>1900 1234</li>
                        <li class="mb-1"><i class="bi bi-envelope me-1"></i>cskh@phoneshop.vn</li>
                        <li class="mb-1"><i class="bi bi-clock me-1"></i>8h00 - 21h00</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Thanh toán</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark">VISA</span>
                        <span class="badge bg-light text-dark">MasterCard</span>
                        <span class="badge bg-light text-dark">MoMo</span>
                        <span class="badge bg-light text-dark">ZaloPay</span>
                        <span class="badge bg-light text-dark">COD</span>
                    </div>
                    <div class="d-flex gap-2 fs-5">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-3">
            <div class="text-center small">© 2026 PhoneShop - Đồ án Nhom 5</div>
        </div>
    </footer>
</div>

{{-- Bootstrap 5 + JQuery CDN --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
