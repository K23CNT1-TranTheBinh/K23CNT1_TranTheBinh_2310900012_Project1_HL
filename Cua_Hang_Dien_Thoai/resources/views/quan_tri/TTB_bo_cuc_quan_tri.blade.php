{{-- Tệp giao diện quản trị --}}
{{--
    bo_cuc_quan_tri.TTB_blade.php — Bố cục dùng chung cho khu quản trị
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Cấu trúc:
    - Sidebar tối (bg-dark) fixed trái 240px, active item nền cam (var --brand:#f97316)
    - Topbar sticky: title + dropdown admin + nút "Xem cửa hàng"
    - Mobile: sidebar thành offcanvas (lg breakpoint), nút hamburger ở topbar
    - Flash message: success/danger
    - @yield('content') + @stack('scripts')

    Cách dùng trong view con:
        @extends('quan_tri.TTB_bo_cuc_quan_tri')
        @section('title', 'Tổng quan')
        @section('content') ... @endsection
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}">
    <title>@yield('title', 'PhoneShop QuanTriVien') — PhoneShop QuanTriVien</title>

    {{-- Bootstrap 5 + Bootstrap Icons + Chart.js (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root { --brand: #f97316; }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f5f6fa;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 240px;
            background: #1f2937;
            position: fixed;
            top: 0; left: 0;
            min-height: 100vh;
            z-index: 1045;
            color: rgba(255,255,255,.65);
            overflow-y: auto;
        }
        .sidebar .sidebar-header {
            padding: 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.65);
            padding: 10px 16px;
            border-radius: 8px;
            margin: 2px 8px;
            font-size: .92rem;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar .nav-link.active {
            background: var(--brand);
            color: #fff;
            font-weight: 600;
        }
        .sidebar .nav-link i { font-size: 1.05rem; }
        .text-brand { color: var(--brand) !important; }
        .bg-brand   { background: var(--brand) !important; }
        .btn-brand {
            background: var(--brand); border-color: var(--brand); color: #fff;
        }
        .btn-brand:hover { background: #ea580c; border-color: #ea580c; color: #fff; }
        .btn-outline-brand {
            border-color: var(--brand); color: var(--brand); background: #fff;
        }
        .btn-outline-brand:hover { background: var(--brand); color: #fff; border-color: var(--brand); }

        .avatar-circle {
            width: 36px; height: 36px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: .9rem;
        }
        .bg-brand-avatar { background: var(--brand); }

        /* ===== Main content ===== */
        .main-content { margin-left: 240px; min-height: 100vh; }

        .topbar-admin {
            background: #fff;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
            position: sticky; top: 0; z-index: 1030;
        }

        .thumb {
            width: 42px; height: 42px; object-fit: contain;
            border-radius: 6px; background: #f8f9fa; border: 1px solid #eee;
        }

        /* Card mượt */
        .card { border: 1px solid #eef0f3; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.03); }
        .card-header { border-bottom: 1px solid #eef0f3; background: #fff; border-radius: 12px 12px 0 0 !important; }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem rgba(249,115,22,.18);
        }

        .nowrap { white-space: nowrap; }

        /* Custom scrollbar danh sách dài */
        .scroll-area { max-height: 24rem; overflow-y: auto; }
        .scroll-area::-webkit-scrollbar { width: 8px; }
        .scroll-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* ===== Responsive: mobile offcanvas ===== */
        @media (max-width: 991.98px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .offcanvas-sidebar {
                background: #1f2937;
                width: 280px;
                color: rgba(255,255,255,.65);
            }
            .offcanvas-sidebar .nav-link {
                color: rgba(255,255,255,.65);
                padding: 10px 16px; border-radius: 8px; margin: 2px 8px;
                display: flex; align-items: center; gap: 10px;
            }
            .offcanvas-sidebar .nav-link.active {
                background: var(--brand); color: #fff; font-weight: 600;
            }
            .offcanvas-sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
            .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        }
    </style>
</head>
<body>

@php
    // Lấy admin hiện tại từ guard 'admin'
    $adminUser = auth('admin')->user();
    // Route name hiện tại để đánh dấu active trên sidebar
    $currentRoute = Route::currentRouteName() ?? '';

    // Menu sidebar (key => [route name, icon, label, prefix match])
    $menu = [
        ['route' => 'admin.dashboard',  'icon' => 'bi-speedometer2',       'label' => 'Tổng quan',     'match' => 'admin.dashboard'],
        ['route' => 'admin.products.index', 'icon' => 'bi-phone',          'label' => 'Sản phẩm',       'match' => 'admin.products'],
        ['route' => 'admin.orders.index',   'icon' => 'bi-bag-check',      'label' => 'Đơn hàng',       'match' => 'admin.orders'],
        ['route' => 'admin.categories.index','icon' => 'bi-tags',          'label' => 'Danh mục',       'match' => 'admin.categories'],
        ['route' => 'admin.brands.index',   'icon' => 'bi-bookmark-star',  'label' => 'Thương hiệu',    'match' => 'admin.brands'],
        ['route' => 'admin.coupons.index',  'icon' => 'bi-ticket-perforated','label' => 'Mã giảm giá',  'match' => 'admin.coupons'],
        ['route' => 'admin.reviews.index',  'icon' => 'bi-star',           'label' => 'Đánh giá',       'match' => 'admin.reviews'],
        ['route' => 'admin.customers.index','icon' => 'bi-people',         'label' => 'Khách hàng',     'match' => 'admin.customers'],
    ];
@endphp

<div class="d-flex">

    {{-- ============ SIDEBAR (desktop) ============ --}}
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-5 fw-bold">
                <i class="bi bi-phone"></i> PhoneShop <span class="text-brand">QuanTriVien</span>
            </a>
            <div class="small mt-1" style="color: rgba(255,255,255,.55);">
                <i class="bi bi-person-circle"></i>
                {{ $adminUser?->full_name ?? 'QuanTriVien' }}
            </div>
        </div>
        <nav class="nav flex-column py-2">
            @foreach ($menu as $item)
                @php $isActive = str_starts_with($currentRoute, $item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ $isActive ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <hr class="border-secondary mx-3 my-2">

            {{-- Link xem cửa hàng --}}
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="bi bi-shop"></i> Xem cửa hàng
            </a>

            {{-- Đăng xuất: POST form --}}
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </button>
            </form>
        </nav>
    </aside>

    {{-- ============ SIDEBAR (mobile offcanvas) ============ --}}
    <div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="adminSidebarMobile">
        <div class="offcanvas-header border-bottom border-secondary py-3 px-3">
            <h5 class="text-white mb-0">
                <i class="bi bi-phone"></i> PhoneShop <span class="text-brand">QuanTriVien</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body py-2">
            <div class="small mb-2 px-3" style="color: rgba(255,255,255,.55);">
                <i class="bi bi-person-circle"></i>
                {{ $adminUser?->full_name ?? 'QuanTriVien' }}
            </div>
            <nav class="nav flex-column">
                @foreach ($menu as $item)
                    @php $isActive = str_starts_with($currentRoute, $item['match']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-link {{ $isActive ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
                <hr class="border-secondary mx-3 my-2">
                <a href="{{ route('home') }}" class="nav-link" target="_blank">
                    <i class="bi bi-shop"></i> Xem cửa hàng
                </a>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </button>
                </form>
            </nav>
        </div>
    </div>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="main-content flex-grow-1">

        {{-- Topbar --}}
        <header class="topbar-admin d-flex align-items-center justify-content-between px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                {{-- Nút hamburger mobile --}}
                <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#adminSidebarMobile">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0 fw-bold">@yield('title', 'Tổng quan')</h1>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Nút xem cửa hàng --}}
                <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-brand">
                    <i class="bi bi-shop"></i>
                    <span class="d-none d-md-inline">Xem cửa hàng</span>
                </a>

                {{-- Dropdown admin --}}
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                        <span class="avatar-circle bg-brand-avatar">
                            {{ mb_substr($adminUser?->full_name ?? 'A', 0, 1) }}
                        </span>
                        <span class="d-none d-md-inline">{{ $adminUser?->full_name ?? 'QuanTriVien' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="bi bi-envelope"></i>
                                {{ $adminUser?->email ?? '' }}
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                Vai trò: <strong>{{ $adminUser?->role ?? 'admin' }}</strong>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Nội dung trang --}}
        <main class="p-3 p-md-4">
            @yield('content')
        </main>

    </div><!-- /.main-content -->
</div><!-- /.d-flex -->

{{-- Bootstrap 5 JS bundle (offcanvas + dropdown) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- Chart.js (load cho dashboard) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@stack('scripts')
</body>
</html>
