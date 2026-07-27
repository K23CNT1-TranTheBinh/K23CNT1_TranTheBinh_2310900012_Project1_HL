{{-- Tệp giao diện quản trị --}}
{{--
    tong_quan.blade.php — Trang tổng quan quản trị
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến nhận từ controller:
    - $counts (mảng: products, orders, customers, reviews, coupons)
    - $revenue (số nguyên: tổng doanh thu đơn completed + shipping)
    - $revenueByDay (collection: mỗi item có {date,value})
    - $recentOrders (collection DonHang, mỗi DonHang có relation customer)
    - $bestSellers (collection: {product_id, product_name, total_sold})
    - $maxSold (số lượng bán lớn nhất)
    - $lowStock (collection SanPham có stock <= 10)
    - $statusMap, $statusCounts (nhãn và số lượng đơn theo trạng thái)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Tổng quan')

@section('content')

{{-- ===== 5 thẻ stat + 1 thẻ doanh thu ===== --}}
<div class="row g-3 mb-3">
    {{-- Sản phẩm --}}
    <div class="col-6 col-lg-2">
        <div class="card border-start border-4 border-primary h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="bi bi-phone fs-5"></i>
                </div>
                <div>
                    <div class="h4 mb-0 fw-bold">{{ $counts['products'] ?? 0 }}</div>
                    <div class="small text-muted">Sản phẩm</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Đơn hàng --}}
    <div class="col-6 col-lg-2">
        <div class="card border-start border-4 border-success h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="bi bi-bag-check fs-5"></i>
                </div>
                <div>
                    <div class="h4 mb-0 fw-bold">{{ $counts['orders'] ?? 0 }}</div>
                    <div class="small text-muted">Đơn hàng</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Khách hàng --}}
    <div class="col-6 col-lg-2">
        <div class="card border-start border-4 border-info h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="bi bi-people fs-5"></i>
                </div>
                <div>
                    <div class="h4 mb-0 fw-bold">{{ $counts['customers'] ?? 0 }}</div>
                    <div class="small text-muted">Khách hàng</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Đánh giá --}}
    <div class="col-6 col-lg-2">
        <div class="card border-start border-4 border-warning h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="bi bi-star fs-5"></i>
                </div>
                <div>
                    <div class="h4 mb-0 fw-bold">{{ $counts['reviews'] ?? 0 }}</div>
                    <div class="small text-muted">Đánh giá</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Mã giảm giá --}}
    <div class="col-6 col-lg-2">
        <div class="card border-start border-4 border-danger h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="bi bi-ticket-perforated fs-5"></i>
                </div>
                <div>
                    <div class="h4 mb-0 fw-bold">{{ $counts['coupons'] ?? 0 }}</div>
                    <div class="small text-muted">Mã giảm giá</div>
                </div>
            </div>
        </div>
    </div>
    {{-- Thẻ doanh thu lớn (gradient cam) --}}
    <div class="col-12 col-lg-2">
        <div class="card text-white h-100" style="background:linear-gradient(135deg,#f97316,#ea580c);">
            <div class="card-body">
                <div class="small opacity-75"><i class="bi bi-cash-stack"></i> Tổng doanh thu</div>
                <div class="h5 fw-bold mt-1">{{ number_format($revenue, 0, ',', '.') }}₫</div>
                <div class="small opacity-75">Đơn hoàn thành + đang giao</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Biểu đồ doanh thu + Trạng thái đơn ===== --}}
<div class="row g-3 mb-3">
    {{-- Biểu đồ doanh thu --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-graph-up-arrow text-brand"></i> Doanh thu theo ngày</strong>
                <span class="badge bg-brand">Chart.js</span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </div>
    </div>
    {{-- Trạng thái đơn hàng --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-list-check text-brand"></i> Trạng thái đơn hàng</strong>
            </div>
            <div class="card-body">
                @foreach ($statusMap as $st => $info)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span><span class="badge {{ $info['class'] }}">{{ $info['label'] }}</span></span>
                        <span class="fw-bold">{{ $statusCounts[$st] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ===== Top bán chạy + Đơn gần đây ===== --}}
<div class="row g-3 mb-3">
    {{-- Top bán chạy --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-trophy text-brand"></i> Top bán chạy</strong>
            </div>
            <div class="card-body">
                @forelse ($bestSellers as $i => $t)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span class="text-truncate">
                                <span class="text-brand fw-bold">#{{ $i + 1 }}</span>
                                {{ $t->product_name }}
                            </span>
                            <span class="fw-semibold">{{ (int) $t->total_sold }} sp</span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-brand"
                                 style="width: {{ $maxSold > 0 ? round($t->total_sold / $maxSold * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Chưa có dữ liệu bán hàng.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Đơn gần đây --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-clock-history text-brand"></i> Đơn gần đây</strong>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-brand">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách</th>
                                <th>Ngày</th>
                                <th>Tổng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($recentOrders as $o)
                            @php $info = $statusMap[$o->status] ?? ['label' => $o->status, 'class' => 'bg-secondary']; @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="text-brand fw-semibold">
                                        {{ $o->order_code }}
                                    </a>
                                </td>
                                <td>{{ $o->customer?->full_name ?? 'Khách vãng lai' }}</td>
                                <td class="small text-muted">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="fw-semibold">{{ number_format($o->final_amount, 0, ',', '.') }}₫</td>
                                <td><span class="badge {{ $info['class'] }}">{{ $info['label'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Chưa có đơn hàng.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Sắp hết hàng ===== --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <strong><i class="bi bi-exclamation-triangle text-danger"></i> Sản phẩm sắp hết hàng (tồn ≤ 10)</strong>
            </div>
            <div class="card-body">
                @forelse ($lowStock as $p)
                    @if ($loop->first)<div class="row g-2">@endif
                    <div class="col-md-6 col-lg-4">
                        <div class="d-flex align-items-center gap-2 border rounded-3 p-2">
                            <img src="{{ $p->image_url }}" alt="" class="thumb"
                                 onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                            <div class="flex-grow-1 text-truncate">
                                <a href="{{ route('admin.products.edit', $p->id) }}"
                                   class="text-decoration-none small fw-semibold text-truncate d-block">
                                    {{ $p->name }}
                                </a>
                                <span class="badge bg-danger">{{ (int) $p->stock }} còn lại</span>
                            </div>
                        </div>
                    </div>
                    @if ($loop->last)</div>@endif
                @empty
                    <p class="text-muted small mb-0">Không có sản phẩm nào sắp hết hàng.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ===== Chart.js: biểu đồ doanh thu theo ngày =====
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 280);
    grad.addColorStop(0, 'rgba(249,115,22,0.45)');
    grad.addColorStop(1, 'rgba(249,115,22,0.02)');

    // Dữ liệu từ $revenueByDay (collection {date, value})
    const labels = @json($revenueByDay->pluck('date'));
    const values = @json($revenueByDay->pluck('value'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu',
                data: values,
                borderColor: '#f97316',
                backgroundColor: grad,
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f97316',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (c) => ' ' + new Intl.NumberFormat('vi-VN').format(c.parsed.y) + ' ₫'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (v) => new Intl.NumberFormat('vi-VN').format(v) + ' ₫' }
                }
            }
        }
    });
</script>
@endpush
