@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Trang tài khoản --}}
@section('title', 'Tài khoản - PhoneShop')

@section('content')
@auth('customer')
@php $u = auth('customer')->user(); @endphp

{{-- Header profile --}}
<div class="hero-gradient text-white py-4">
    <div class="container">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="bg-white text-brand d-flex align-items-center justify-content-center fw-bold"
                 style="width: 70px; height: 70px; border-radius: 50%; font-size: 1.8rem;">
                {{ strtoupper(mb_substr($u->full_name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <h3 class="fw-bold mb-0">{{ $u->full_name }}</h3>
                <div class="small"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</div>
            </div>
            <a href="{{ route('account.orders') }}" class="btn btn-light text-brand fw-semibold">
                <i class="bi bi-bag-check me-1"></i>Đơn hàng của tôi
            </a>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-md-4 col-lg-3">
            <div class="list-group shadow-sm">
                <a href="{{ route('account') }}" class="list-group-item list-group-item-action active" style="background: var(--brand); border-color: var(--brand);">
                    <i class="bi bi-person-circle me-2"></i>Thông tin tài khoản
                </a>
                <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-bag-check me-2"></i>Đơn hàng của tôi
                </a>
                <a href="{{ route('cart') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-bag me-2"></i>Giỏ hàng
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                    </button>
                </form>
            </div>
        </div>

        {{-- Profile --}}
        <div class="col-md-8 col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-vcard text-brand me-2"></i>Thông tin cá nhân</h5>
                    <form action="{{ route('account.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control" maxlength="100"
                                       value="{{ old('full_name', $u->full_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" maxlength="100"
                                       value="{{ old('email', $u->email) }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" maxlength="20" pattern="(?:0|\+84)\d{9,10}"
                                       value="{{ old('phone', $u->phone) }}" placeholder="09xxxxxxxx">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="address" class="form-control" maxlength="500"
                                       value="{{ old('address', $u->address) }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-muted">Tham gia: {{ $u->created_at ? $u->created_at->format('d/m/Y') : '—' }}</span>
                            <button class="btn btn-brand" type="submit">
                                <i class="bi bi-save me-1"></i>Lưu thông tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock text-brand me-2"></i>Đổi mật khẩu</h5>
                    <form action="{{ route('account.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nhập lại mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                            </div>
                        </div>
                        <button class="btn btn-outline-brand mt-3" type="submit">
                            <i class="bi bi-key me-1"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
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
            <p class="text-muted">Bạn cần đăng nhập để xem thông tin tài khoản.</p>
            <a href="{{ route('login') }}" class="btn btn-brand"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
        </div>
    </div>
</div>
@endguest

@endsection
