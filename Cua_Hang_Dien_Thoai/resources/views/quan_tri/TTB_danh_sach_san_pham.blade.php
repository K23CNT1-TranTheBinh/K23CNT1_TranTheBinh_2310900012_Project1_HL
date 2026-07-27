{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_san_pham.blade.php — Danh sách sản phẩm
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $products: LengthAwarePaginator (15/trang), mỗi SanPham có relation category, brand
      và accessor current_price, discount_percent
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Sản phẩm')

@section('content')

{{-- Thanh công cụ: tìm kiếm + thêm --}}
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form method="get" action="{{ route('admin.products.index') }}" class="d-flex gap-2 flex-grow-1" style="max-width:480px;">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên sản phẩm...">
                <button class="btn btn-brand text-white" type="submit">Tìm</button>
            </div>
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn btn-brand text-white">
            <i class="bi bi-plus-lg"></i> Thêm sản phẩm
        </a>
    </div>
</div>

{{-- Bảng sản phẩm --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-phone text-brand"></i> Danh sách sản phẩm</strong>
        <span class="text-muted small">Tổng: <strong>{{ $products->total() }}</strong> SP</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th class="text-end">Giá gốc</th>
                        <th class="text-end">Giá sale</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="140">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $p)
                    @php $lowStock = (int) $p->stock <= 10; @endphp
                    <tr>
                        <td><img src="{{ $p->image_url }}" alt="" class="thumb"
                                 onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'"></td>
                        <td>
                            <a href="{{ route('admin.products.edit', $p->id) }}" class="fw-semibold text-decoration-none">
                                {{ $p->name }}
                            </a>
                            @if ($p->is_featured)
                                <i class="bi bi-star-fill text-warning ms-1" title="Nổi bật"></i>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark">{{ $p->category?->name ?? '—' }}</span></td>
                        <td>{{ $p->brand?->name ?? '—' }}</td>
                        <td class="text-end">{{ number_format($p->price, 0, ',', '.') }}₫</td>
                        <td class="text-end">
                            @if ($p->sale_price && $p->sale_price < $p->price)
                                <span class="text-danger">{{ number_format($p->sale_price, 0, ',', '.') }}₫</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $lowStock ? 'bg-danger' : 'bg-success' }}">{{ (int) $p->stock }}</span>
                        </td>
                        <td class="text-center">
                            @if ((int) $p->status === 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-outline-brand" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            {{-- Form xoá: DELETE + @csrf + confirm JS --}}
                            <form method="post" action="{{ route('admin.products.destroy', $p->id) }}" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Chưa có sản phẩm nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($products->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
