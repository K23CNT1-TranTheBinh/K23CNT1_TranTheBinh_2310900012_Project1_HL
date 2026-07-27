@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Đăng nhập --}}
@section('title', 'Đăng nhập - PhoneShop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    {{-- ThuongHieu --}}
                    <div class="text-center mb-4">
                        <div class="hero-gradient d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; border-radius: 50%;">
                            <i class="bi bi-person-fill-lock fs-2 text-white"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Đăng nhập</h3>
                        <p class="text-muted mb-0">Chào mừng bạn quay lại PhoneShop</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email hoặc tên đăng nhập</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" name="email" class="form-control" required autofocus
                                       value="{{ old('email') }}" placeholder="Nhập email hoặc tên đăng nhập">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                        </button>
                    </form>

                    <div class="text-center">
                        <span class="small">Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="text-brand fw-semibold text-decoration-none">Đăng ký ngay</a>
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
