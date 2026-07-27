@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Cửa hàng --}}
@section('title', $title ?? 'Danh sách sản phẩm - PhoneShop')

@section('content')

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">{{ $title ?? 'Danh sách sản phẩm' }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Sidebar filter --}}
        <aside class="col-lg-3">
            <div class="d-lg-none mb-3">
                <button class="btn btn-brand w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel me-2"></i>Bộ lọc
                </button>
            </div>
            <div class="collapse d-lg-block" id="filterCollapse">
                <form action="{{ route('catalog') }}" method="GET" class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        {{-- Giu query search --}}
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <h6 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-brand me-2"></i>Bộ lọc</h6>

                        {{-- Danh muc --}}
                        <div class="mb-3">
                            <label class="fw-semibold small mb-2">Danh mục</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat_all" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label small" for="cat_all">Tất cả</label>
                            </div>
                            @foreach($categories as $c)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" id="cat_{{ $c->id }}" value="{{ $c->slug }}" {{ request('category') == $c->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label small" for="cat_{{ $c->id }}">{{ $c->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Thuong hieu --}}
                        <div class="mb-3">
                            <label class="fw-semibold small mb-2">Thương hiệu</label>
                            @foreach($brands as $b)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brand[]" id="brand_{{ $b->id }}" value="{{ $b->slug }}" {{ in_array($b->slug, (array)request('brand')) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="brand_{{ $b->id }}">{{ $b->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Khoang gia --}}
                        <div class="mb-3">
                            <label class="fw-semibold small mb-2">Khoảng giá</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="number" name="min_price" class="form-control" placeholder="Từ" value="{{ request('min_price') }}">
                                <span class="input-group-text">-</span>
                                <input type="number" name="max_price" class="form-control" placeholder="Đến" value="{{ request('max_price') }}">
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPrice(0, 5000000)">< 5tr</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPrice(5000000, 10000000)">5-10tr</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPrice(10000000, 20000000)">10-20tr</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPrice(20000000, 50000000)">>20tr</button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-brand btn-sm"><i class="bi bi-funnel me-1"></i>Lọc</button>
                            <a href="{{ route('catalog') }}" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        {{-- Grid SP --}}
        <div class="col-lg-9">
            {{-- Toolbar --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Tìm thấy <strong class="text-dark">{{ $products->total() }}</strong> sản phẩm
                </div>
                <form action="{{ route('catalog') }}" method="GET" class="d-flex align-items-center gap-2" id="sortForm">
                    {{-- Giu query khac --}}
                    @foreach(request()->except('sort','page') as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <label class="small text-muted">Sắp xếp:</label>
                    <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="document.getElementById('sortForm').submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                        <option value="best_selling" {{ request('sort') == 'best_selling' ? 'selected' : '' }}>Bán chạy</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Không tìm thấy sản phẩm</h4>
                        <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                        <a href="{{ route('catalog') }}" class="btn btn-brand"><i class="bi bi-arrow-clockwise me-1"></i>Xóa lọc</a>
                    </div>
                </div>
            @else
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                    @foreach($products as $p)
                        <div class="col">
                            <div class="card border-0 shadow-sm h-100 product-card">
                                <a href="{{ route('product.show', $p->slug) }}" class="text-decoration-none text-dark">
                                    <img src="{{ $p->image_url }}" class="card-img-top" alt="{{ $p->name }}" style="height:220px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2">{{ $p->name }}</h6>
                                        <p class="small text-muted mb-2">{{ Str::limit($p->short_desc ?? $p->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold text-brand">{{ number_format($p->current_price, 0, ',', '.') }}đ</div>
                                                @if($p->sale_price && $p->sale_price < $p->price)
                                                    <div class="price-strike">{{ number_format($p->price, 0, ',', '.') }}đ</div>
                                                @endif
                                            </div>
                                            <span class="badge bg-brand">Mua</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function setPrice(min, max) {
    document.querySelector('[name="min_price"]').value = min;
    document.querySelector('[name="max_price"]').value = max;
    document.querySelector('[name="min_price"]').form.submit();
}
</script>
@endpush

@endsection
