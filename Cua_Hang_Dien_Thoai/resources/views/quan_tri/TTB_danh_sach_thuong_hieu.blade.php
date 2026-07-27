{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_thuong_hieu.blade.php — Danh sách thương hiệu
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $brands: collection ThuongHieu
    Route resource (except show): admin.brands.index/create/store/edit/update/destroy
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Thương hiệu')

@section('content')

{{-- Thanh công cụ --}}
<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-bookmark-star text-brand"></i> Danh sách thương hiệu</strong>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-brand text-white">
            <i class="bi bi-plus-lg"></i> Thêm thương hiệu
        </a>
    </div>
</div>

{{-- Bảng thương hiệu --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>Tên thương hiệu</th>
                        <th>Slug</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="140">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($brands as $b)
                    <tr>
                        <td class="text-muted">{{ $b->id }}</td>
                        <td class="fw-semibold">{{ $b->name }}</td>
                        <td><code class="small">{{ $b->slug }}</code></td>
                        <td class="text-center">
                            @if ((int) $b->status === 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.brands.edit', $b->id) }}" class="btn btn-sm btn-outline-brand" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="{{ route('admin.brands.destroy', $b->id) }}" class="d-inline"
                                  onsubmit="return confirm('Xoá thương hiệu này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có thương hiệu nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
