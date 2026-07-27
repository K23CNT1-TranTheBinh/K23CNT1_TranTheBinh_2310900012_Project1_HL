{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_don_hang.blade.php — Danh sách đơn hàng
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $orders: LengthAwarePaginator (15/trang), mỗi DonHang có relation user
    Route update status: PATCH admin.orders.updateStatus({id}) — body: status
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Đơn hàng')

@php
    use App\Models\DonHang;
    // Mapping trạng thái
    $statusMap = [
        DonHang::STATUS_PENDING   => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
        DonHang::STATUS_CONFIRMED => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
        DonHang::STATUS_SHIPPING  => ['label' => 'Đang giao',   'class' => 'bg-primary'],
        DonHang::STATUS_COMPLETED => ['label' => 'Hoàn thành',   'class' => 'bg-success'],
        DonHang::STATUS_CANCELLED => ['label' => 'Đã huỷ',       'class' => 'bg-danger'],
    ];
    // Nhãn phương thức thanh toán
    $paymentMethodLabels = [
        'cod'      => 'Tiền mặt (COD)',
        'banking'  => 'Chuyển khoản',
        'momo'     => 'Ví MoMo',
    ];
    $transitionMap = [
        DonHang::STATUS_PENDING => [DonHang::STATUS_CONFIRMED, DonHang::STATUS_CANCELLED],
        DonHang::STATUS_CONFIRMED => [DonHang::STATUS_SHIPPING, DonHang::STATUS_CANCELLED],
        DonHang::STATUS_SHIPPING => [DonHang::STATUS_COMPLETED],
        DonHang::STATUS_COMPLETED => [],
        DonHang::STATUS_CANCELLED => [],
    ];
    $statusFilter = request('status', '');
    $q = request('q', '');
@endphp

@section('content')

{{-- Thanh công cụ: filter + search --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="{{ route('admin.orders.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Filter theo trạng thái (link query string ?status=) --}}
            <div class="input-group" style="max-width:240px;">
                <span class="input-group-text bg-light"><i class="bi bi-funnel"></i></span>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statusMap as $k => $info)
                        <option value="{{ $k }}" {{ $statusFilter === $k ? 'selected' : '' }}>{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Tìm kiếm theo mã đơn --}}
            <div class="input-group flex-grow-1" style="max-width:320px;">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Tìm mã đơn...">
                <button class="btn btn-brand text-white" type="submit">Tìm</button>
            </div>
            @if ($statusFilter !== '' || $q !== '')
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i> Xoá bộ lọc
                </a>
            @endif
            <div class="ms-auto small text-muted">
                Tổng cộng: <strong>{{ $orders->total() }}</strong> đơn
            </div>
        </form>
    </div>
</div>

{{-- Bảng đơn hàng --}}
<div class="card">
    <div class="card-header bg-white">
        <strong><i class="bi bi-bag-check text-brand"></i> Danh sách đơn hàng</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th class="text-end">Tổng tiền</th>
                        <th>PT thanh toán</th>
                        <th class="text-center">TT thanh toán</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($orders as $o)
                    @php
                        $info = $statusMap[$o->status] ?? ['label' => $o->status, 'class' => 'bg-secondary'];
                        $allowedStatuses = array_merge([$o->status], $transitionMap[$o->status] ?? []);
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $o->id) }}" class="text-brand fw-bold">
                                {{ $o->order_code }}
                            </a>
                        </td>
                        <td>{{ $o->customer?->full_name ?? 'Khách vãng lai' }}</td>
                        <td class="small text-muted">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-end fw-semibold">{{ number_format($o->final_amount, 0, ',', '.') }}₫</td>
                        <td class="small">{{ $paymentMethodLabels[$o->payment_method] ?? ucfirst($o->payment_method) }}</td>
                        <td class="text-center">
                            @if ($o->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Inline đổi trạng thái: PATCH admin.orders.updateStatus --}}
                            <form method="post" action="{{ route('admin.orders.updateStatus', $o->id) }}" class="d-inline-block">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="min-width:140px;"
                                        {{ count($allowedStatuses) === 1 ? 'disabled' : '' }}
                                        onchange="this.form.submit()">
                                    @foreach ($allowedStatuses as $k)
                                        <option value="{{ $k }}" {{ $o->status === $k ? 'selected' : '' }}>{{ $statusMap[$k]['label'] ?? $k }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-sm btn-outline-brand" title="Xem">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($orders->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
