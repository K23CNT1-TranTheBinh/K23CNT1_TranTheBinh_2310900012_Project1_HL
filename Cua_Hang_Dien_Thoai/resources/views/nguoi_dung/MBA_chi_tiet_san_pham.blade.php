@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung') {{-- Chi tiết sản phẩm --}}
@section('title', $product->name . ' - PhoneShop')

@section('content')

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog') }}" class="text-decoration-none">Điện thoại</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('catalog.category', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Anh --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="bg-light rounded-3 text-center mb-3" style="aspect-ratio: 1/1;">
                        <img id="mainImage" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="w-100 h-100" style="object-fit: contain; padding: 16px;"
                             onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                    </div>
                    @php
                        $images = [];
                        if(!empty($product->images)) {
                            $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                        }
                        $allImages = array_unique(array_merge([$product->image], is_array($images) ? $images : []));
                    @endphp
                    @if(count($allImages) > 1)
                        <div class="d-flex gap-2 overflow-auto scrollbar-thin">
                            @foreach($allImages as $img)
                                <div class="bg-light border rounded-3" style="width: 70px; height: 70px; flex-shrink: 0; cursor: pointer;" onclick="swapImage(this)">
                                    <img src="{{ $product->resolveImageUrl($img) }}" class="w-100 h-100" style="object-fit: contain; padding: 4px;"
                                         onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ho tro ben duoi anh --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><i class="bi bi-truck text-brand me-2"></i>Giao hàng miễn phí nội thành</li>
                        <li class="mb-2"><i class="bi bi-shield-check text-brand me-2"></i>Bảo hành chính hãng 12 tháng</li>
                        <li><i class="bi bi-arrow-repeat text-brand me-2"></i>Đổi trả trong 7 ngày</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Thong tin --}}
        <div class="col-lg-4">
            @if($product->brand)
                <a href="{{ route('catalog.brand', $product->brand->slug) }}" class="badge bg-light text-brand text-decoration-none mb-2">{{ $product->brand->name }}</a>
            @endif
            <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>

            {{-- Rating --}}
            @php $avg = $product->avg_rating; @endphp
            <div class="mb-3">
                @if($avg > 0)
                    <span class="text-warning me-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avg)) <i class="bi bi-star-fill"></i> @else <i class="bi bi-star"></i> @endif
                        @endfor
                    </span>
                    <span class="small text-muted">{{ number_format($avg, 1) }} / 5 ({{ $product->reviews->count() }} đánh giá)</span>
                @else
                    <span class="small text-muted"><i class="bi bi-star"></i> Chưa có đánh giá</span>
                @endif
                <span class="small text-muted ms-3"><i class="bi bi-eye me-1"></i>{{ $product->views }} lượt xem</span>
            </div>

            {{-- Gia --}}
            @php
                $current  = $product->current_price;
                $discount = $product->discount_percent;
            @endphp
            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="d-flex align-items-baseline gap-3 flex-wrap">
                    <span class="h2 fw-bold text-brand mb-0">{{ number_format($current, 0, ',', '.') }}₫</span>
                    @if($discount > 0)
                        <span class="price-strike">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        <span class="badge bg-danger">-{{ $discount }}%</span>
                    @endif
                </div>
            </div>

            {{-- Trang thai --}}
            <div class="mb-3">
                @if($product->stock > 0)
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Còn hàng ({{ $product->stock }} sản phẩm)</span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i>Hết hàng</span>
                @endif
            </div>

            {{-- Mo ta ngan --}}
            @if($product->short_desc)
                <p class="text-muted">{{ $product->short_desc }}</p>
            @endif

            @if((int) $product->stock > 0)
                {{-- So luong + form --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="fw-semibold">Số lượng:</label>
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="qtyChange(-1)"><i class="bi bi-dash"></i></button>
                        <input type="number" id="qty" class="form-control qty-input" value="1" min="1" max="{{ $product->stock }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="qtyChange(1)"><i class="bi bi-plus"></i></button>
                    </div>
                </div>

                {{-- Them vao gio --}}
                <form action="{{ route('cart.add') }}" method="POST" class="d-flex gap-2 mb-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qtyHidden" value="1">
                    <button type="submit" class="btn btn-outline-brand flex-fill">
                        <i class="bi bi-bag-plus me-1"></i>Thêm vào giỏ
                    </button>
                </form>

                {{-- Mua ngay --}}
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qtyHidden2" value="1">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn btn-brand w-100">
                        <i class="bi bi-lightning-charge me-1"></i>Mua ngay
                    </button>
                </form>
            @else
                <button type="button" class="btn btn-secondary w-100" disabled>
                    <i class="bi bi-x-circle me-1"></i>Sản phẩm tạm hết hàng
                </button>
            @endif
        </div>

        {{-- Sidebar phai --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-brand me-2"></i>Thông tin mua hàng</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Hàng chính hãng 100%</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Giao nhanh 2h nội thành</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Trả góp 0% lãi suất</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Thu cũ đổi mới</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mt-5">
        <ul class="nav nav-tabs" id="prodTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#desc" type="button"><i class="bi bi-text-paragraph me-1"></i>Mô tả</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#specs" type="button"><i class="bi bi-list-check me-1"></i>Thông số</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#review" type="button"><i class="bi bi-chat-dots me-1"></i>Đánh giá ({{ $product->reviews->count() }})</button>
            </li>
        </ul>

        <div class="tab-content pt-4" id="prodTabContent">
            {{-- Mo ta --}}
            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            {{-- Thong so --}}
            <div class="tab-pane fade" id="specs" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @php
                            $specs = $product->specs;
                            if(!is_array($specs)) {
                                $decoded = json_decode($specs, true);
                                $specs = is_array($decoded) ? $decoded : [];
                            }
                        @endphp
                        @if(!empty($specs))
                            <table class="table table-striped mb-0">
                                <tbody>
                                    @foreach($specs as $label => $value)
                                        <tr>
                                            <th style="width: 40%;" class="fw-semibold">{{ $label }}</th>
                                            <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Chưa có thông số kỹ thuật chi tiết.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Danh gia --}}
            <div class="tab-pane fade" id="review" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="fw-bold mb-3">Đánh giá từ khách hàng</h5>
                        @if($product->reviews->isEmpty())
                            <div class="card border-0 shadow-sm text-center py-4">
                                <div class="card-body">
                                    <i class="bi bi-chat-square display-4 text-muted"></i>
                                    <p class="mt-2 mb-0 text-muted">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                                </div>
                            </div>
                        @else
                            @foreach($product->reviews as $r)
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-circle me-2">{{ strtoupper(mb_substr($r->customer->full_name ?? 'K', 0, 1)) }}</div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $r->customer->full_name ?? 'Khách' }}</div>
                                                <div class="small text-muted">{{ $r->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $r->rating) <i class="bi bi-star-fill"></i> @else <i class="bi bi-star"></i> @endif
                                                @endfor
                                            </span>
                                        </div>
                                        <p class="mb-0">{{ $r->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="col-lg-5">
                        @auth('customer')
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-brand me-2"></i>Viết đánh giá</h5>
                                    @if($hasReviewed)
                                        <div class="alert alert-success mb-0">
                                            <i class="bi bi-check-circle me-1"></i>Bạn đã đánh giá sản phẩm này.
                                        </div>
                                    @elseif(!$canReview)
                                        <div class="alert alert-light border mb-0">
                                            <i class="bi bi-bag-check me-1 text-brand"></i>Bạn có thể đánh giá sau khi đơn hàng chứa sản phẩm này hoàn thành.
                                        </div>
                                    @else
                                        <form action="{{ route('review.store', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Số sao</label>
                                                <select name="rating" class="form-select" required>
                                                    <option value="5">★★★★★ - Tuyệt vời</option>
                                                    <option value="4">★★★★ - Hài lòng</option>
                                                    <option value="3">★★★ - Bình thường</option>
                                                    <option value="2">★★ - Không hài lòng</option>
                                                    <option value="1">★ - Tệ</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nội dung</label>
                                                <textarea name="comment" class="form-control" rows="4" minlength="5" maxlength="500"
                                                          placeholder="Chia sẻ trải nghiệm của bạn..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-brand w-100"><i class="bi bi-send me-1"></i>Gửi đánh giá</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <i class="bi bi-lock display-4 text-muted"></i>
                                    <h6 class="mt-3">Đăng nhập để đánh giá</h6>
                                    <p class="small text-muted">Bạn cần đăng nhập để viết đánh giá cho sản phẩm này.</p>
                                    <a href="{{ route('login') }}" class="btn btn-outline-brand btn-sm">Đăng nhập</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SP lien quan --}}
    @if(!empty($related) && $related->count() > 0)
        <div class="mt-5">
            <h4 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap text-brand me-2"></i>Sản phẩm liên quan</h4>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach($related as $r)
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100 product-card">
                            <a href="{{ route('product.show', $r->slug) }}" class="text-decoration-none text-dark">
                                <img src="{{ $r->image_url }}" class="card-img-top" alt="{{ $r->name }}" style="height:220px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2">{{ $r->name }}</h6>
                                    <p class="small text-muted mb-2">{{ Str::limit($r->short_desc ?? $r->description, 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-brand">{{ number_format($r->current_price, 0, ',', '.') }}đ</div>
                                            @if($r->sale_price && $r->sale_price < $r->price)
                                                <div class="price-strike">{{ number_format($r->price, 0, ',', '.') }}đ</div>
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
        </div>
    @endif
</div>

@push('scripts')
<script>
function swapImage(thumb) {
    var src = $(thumb).find('img').attr('src');
    $('#mainImage').attr('src', src);
}
function qtyChange(delta) {
    var input = document.getElementById('qty');
    var max = parseInt(input.max) || 99;
    var v = parseInt(input.value) + delta;
    if (v < 1) v = 1;
    if (v > max) v = max;
    input.value = v;
    document.getElementById('qtyHidden').value = v;
    document.getElementById('qtyHidden2').value = v;
}
</script>
@endpush

@endsection
