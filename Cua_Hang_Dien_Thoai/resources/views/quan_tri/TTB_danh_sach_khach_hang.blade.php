{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_khach_hang.blade.php — Danh sách khách hàng
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $customers: collection KhachHang (table 'users', có orders_count qua withCount('orders'))
    Route: admin.customers.index, admin.customers.toggle({id}, PATCH)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Khách hàng')

@section('content')

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-people text-brand"></i> Danh sách khách hàng ({{ $customers->count() }})</strong>
        <span class="text-muted small">Quản lý tài khoản khách hàng</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Khách hàng</th>
                        <th>Email / SĐT</th>
                        <th>Địa chỉ</th>
                        <th class="text-center">Số đơn</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($customers as $u)
                    @php $firstChar = mb_substr($u->full_name, 0, 1); @endphp
                    <tr>
                        <td class="text-muted">{{ $u->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-circle bg-brand-avatar">{{ mb_strtoupper($firstChar) }}</span>
                                <div>
                                    <div class="fw-semibold">{{ $u->full_name }}</div>
                                    <div class="small text-muted">@user{{ $u->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-envelope"></i> {{ $u->email }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone"></i>
                                {{ $u->phone ?: '(chưa cập nhật)' }}
                            </div>
                        </td>
                        <td class="small text-truncate" style="max-width:200px;" title="{{ $u->address }}">
                            {{ $u->address ?: '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $u->orders_count ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            @if ((int) $u->status === 1)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-danger">Bị khoá</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Toggle khoá/mở: PATCH admin.customers.toggle --}}
                            <form method="post" action="{{ route('admin.customers.toggle', $u->id) }}"
                                  onsubmit="return confirm('{{ (int) $u->status === 1 ? 'Khoá tài khoản khách hàng này?' : 'Mở khoá tài khoản này?' }}');">
                                @csrf
                                @method('PATCH')
                                @if ((int) $u->status === 1)
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Khoá tài khoản">
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mở khoá">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Chưa có khách hàng nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
