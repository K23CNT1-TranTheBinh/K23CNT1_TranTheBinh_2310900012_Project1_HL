@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Đăng ký --}}
@section('title', 'Đăng ký - PhoneShop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    {{-- ThuongHieu --}}
                    <div class="text-center mb-4">
                        <div class="hero-gradient d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; border-radius: 50%;">
                            <i class="bi bi-person-plus-fill fs-2 text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Đăng ký tài khoản</h3>
                        <p class="text-muted mb-0">Tạo tài khoản để mua sắm dễ dàng hơn</p>
                    </div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control" required value="{{ old('full_name') }}" placeholder="Nguyễn Văn A">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                                <input type="text" name="phone" class="form-control" pattern="(?:0|\+84)\d{9,10}" value="{{ old('phone') }}" placeholder="0912345678">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" required minlength="8" placeholder="Tối thiểu 8 ký tự">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8" placeholder="Nhập lại mật khẩu">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password_confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="agree" class="form-check-input" id="agree" required>
                            <label class="form-check-label small" for="agree">Tôi đồng ý với <a href="#" class="text-brand">Điều khoản</a> & <a href="#" class="text-brand">Chính sách bảo mật</a> của PhoneShop</label>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 mb-3">
                            <i class="bi bi-person-plus me-1"></i>Đăng ký
                        </button>
                    </form>

                    <div class="text-center">
                        <span class="small">Đã có tài khoản?
                            <a href="{{ route('login') }}" class="text-brand fw-semibold text-decoration-none">Đăng nhập</a>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePw(id, btn) {
    var input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="bi bi-eye"></i>';
    }
}
</script>
@endpush

@endsection
