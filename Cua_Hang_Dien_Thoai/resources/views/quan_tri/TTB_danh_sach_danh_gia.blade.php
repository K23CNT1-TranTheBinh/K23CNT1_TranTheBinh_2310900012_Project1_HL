{{-- Tệp giao diện quản trị --}}
{{--
    danh_sach_danh_gia.blade.php — Danh sách đánh giá
    Người làm: Trần Thế Bình (TTB_) - Backend admin

    Biến:
    - $reviews: collection DanhGia (có relation product, user)
    Route: admin.reviews.index, admin.reviews.toggle({id}, PATCH), admin.reviews.destroy({id}, DELETE)
--}}
@extends('quan_tri.TTB_bo_cuc_quan_tri')
@section('title', 'Đánh giá')

@php
    // Helper vẽ sao (Bootstrap Icons)
    $renderStars = function ($n) {
        $n = (int) $n;
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $n
                ? '<i class="bi bi-star-fill text-warning"></i>'
                : '<i class="bi bi-star text-muted"></i>';
        }
        return $html;
    };
@endphp

@section('content')

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-star text-brand"></i> Danh sách đánh giá ({{ $reviews->count() }})</strong>
        <span class="text-muted small">Duyệt / Ẩn đánh giá khách hàng</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Sản phẩm</th>
                        <th>Người đánh giá</th>
                        <th class="text-center">Sao</th>
                        <th>Bình luận</th>
                        <th>Ngày</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" width="140">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($reviews as $r)
                    <tr>
                        <td class="text-muted">{{ $r->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($r->product?->image)
                                    <img src="{{ $r->product->image_url }}" alt="" class="thumb" style="width:42px;height:42px;"
                                         onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                @endif
                                <span class="text-truncate" style="max-width:200px;" title="{{ $r->product?->name }}">
                                    {{ $r->product?->name ?? '—' }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $r->customer?->full_name ?? 'Khách' }}</td>
                        <td class="text-center">
                            <div class="text-nowrap">{!! $renderStars($r->rating) !!}</div>
                            <span class="badge bg-light text-dark small">{{ (int) $r->rating }}/5</span>
                        </td>
                        <td class="small text-truncate" style="max-width:280px;" title="{{ $r->comment }}">
                            @if ($r->comment)
                                {{ $r->comment }}
                            @else
                                <em class="text-muted">(không có bình luận)</em>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $r->created_at?->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if ((int) $r->status === 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Toggle duyệt/ẩn: PATCH admin.reviews.toggle --}}
                            <form method="post" action="{{ route('admin.reviews.toggle', $r->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                @if ((int) $r->status === 1)
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Ẩn đánh giá">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt đánh giá">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                @endif
                            </form>
                            {{-- Xoá: DELETE admin.reviews.destroy --}}
                            <form method="post" action="{{ route('admin.reviews.destroy', $r->id) }}" class="d-inline"
                                  onsubmit="return confirm('Xoá đánh giá này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Chưa có đánh giá nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
