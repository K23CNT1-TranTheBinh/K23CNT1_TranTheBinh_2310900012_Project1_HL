@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Đơn hàng của tôi --}}
@section('title', 'Đơn hàng của tôi - PhoneShop')

@section('content')
@auth('customer')

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account') }}" class="text-decoration-none">Tài khoản</a></li>
            <li class="breadcrumb-item active">Đơn hàng của tôi</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0"><i class="bi bi-bag-check text-brand me-2"></i>Đơn hàng của tôi</h2>
        <a href="{{ route('catalog') }}" class="btn btn-outline-brand">
            <i class="bi bi-bag me-1"></i>Mua sắm thêm
        </a>
    </div>

    @if(empty($orders) || $orders->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-bag-x display-1 text-muted"></i>
                <h4 class="mt-3">Chưa có đơn hàng</h4>
                <p class="text-muted">Bạn chưa đặt đơn hàng nào. Khám phá cửa hàng và đặt hàng ngay!</p>
                <a href="{{ route('catalog') }}" class="btn btn-brand"><i class="bi bi-bag me-1"></i>Mua sắm ngay</a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th class="text-center">Số SP</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center pe-3">Xem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $o)
                                <tr>
                                    <td class="ps-3">
                                        <a href="{{ route('account.order.detail', $o->id) }}" class="text-brand fw-semibold text-decoration-none">
                                            {{ $o->order_code }}
                                        </a>
                                    </td>
                                    <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $o->details_count }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($o->final_amount, 0, ',', '.') }}₫</td>
                                    <td class="text-center">
                                        @switch($o->status)
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
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('account.order.detail', $o->id) }}" class="btn btn-sm btn-outline-brand">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($o->status === 'pending')
                                            <form action="{{ route('account.order.cancel', $o->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Hủy đơn">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(method_exists($orders, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    @endif

</div>

@endauth

@guest('customer')
<div class="container py-5">
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="bi bi-lock display-1 text-muted"></i>
            <h4 class="mt-3">Vui lòng đăng nhập</h4>
            <p class="text-muted">Bạn cần đăng nhập để xem đơn hàng của mình.</p>
            <a href="{{ route('login') }}" class="btn btn-brand"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
        </div>
    </div>
</div>
@endguest

@endsection
