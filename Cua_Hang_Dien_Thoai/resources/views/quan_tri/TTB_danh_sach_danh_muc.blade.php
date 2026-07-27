{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_danh_muc.blade.php — Danh sách danh mục
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $categories: collection DanhMuc (có thể có withCount('products'))
    Route resource (except show): admin.categories.index/create/store/edit/update/destroy
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Danh mục')

@section('content')

{{-- Thanh công cụ --}}
<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-tags text-brand"></i> Danh sách danh mục</strong>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-brand text-white">
            <i class="bi bi-plus-lg"></i> Thêm danh mục
        </a>
    </div>
</div>

{{-- Bảng danh mục --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Mô tả</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="140">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($categories as $c)
                    <tr>
                        <td class="text-muted">{{ $c->id }}</td>
                        <td class="fw-semibold">{{ $c->name }}</td>
                        <td><code class="small">{{ $c->slug }}</code></td>
                        <td class="small text-muted text-truncate" style="max-width:260px;" title="{{ $c->description }}">
                            {{ $c->description ?: '—' }}
                        </td>
                        <td class="text-center">
                            @if ((int) $c->status === 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn btn-sm btn-outline-brand" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            {{-- Form xoá --}}
                            <form method="post" action="{{ route('admin.categories.destroy', $c->id) }}" class="d-inline"
                                  onsubmit="return confirm('Xoá danh mục này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có danh mục nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
