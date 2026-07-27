{{-- Thành phần thẻ sản phẩm --}}
@props(['product'])

@php
    $current   = $product->current_price;
    $old       = $product->price;
    $discount  = $product->discount_percent;
    $avg       = $product->avg_rating;
    $imageUrl  = $product->image_url;
@endphp

<div class="card product-card h-100 border-0 shadow-sm">
    <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark d-block">
        <div class="position-relative bg-light" style="aspect-ratio: 1/1;">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                 class="w-100 h-100" style="object-fit: contain; padding: 14px;" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('images/anh_dien_thoai_mac_dinh.svg') }}'">
            @if($discount > 0)
                <span class="badge bg-danger position-absolute top-0 start-0 m-2">-{{ $discount }}%</span>
            @endif
            @if($product->is_new)
                <span class="badge bg-brand position-absolute top-0 end-0 m-2">Mới</span>
            @endif
        </div>
        <div class="card-body p-3">
            @if($product->brand)
                <div class="small text-muted text-uppercase fw-semibold mb-1">{{ $product->brand->name }}</div>
            @endif
            <h6 class="card-title line-clamp-2 mb-2" style="font-size: .95rem;">{{ $product->name }}</h6>
            <div class="mb-2 small">
                @if($avg > 0)
                    <span class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avg)) <i class="bi bi-star-fill"></i> @else <i class="bi bi-star"></i> @endif
                        @endfor
                    </span>
                    <span class="text-muted">({{ $product->reviews_count ?? $product->reviews()->count() }} đánh giá)</span>
                @else
                    <span class="text-muted"><i class="bi bi-star"></i> Chưa có đánh giá</span>
                @endif
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fw-bold text-brand fs-6">{{ number_format($current, 0, ',', '.') }}₫</span>
                @if($discount > 0)
                    <span class="price-strike">{{ number_format($old, 0, ',', '.') }}₫</span>
                @endif
            </div>
        </div>
    </a>
    <div class="card-footer bg-transparent border-0 p-3 pt-0">
        @if((int) $product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-brand btn-sm w-100">
                    <i class="bi bi-bag-plus me-1"></i> Thêm vào giỏ
                </button>
            </form>
        @else
            <button type="button" class="btn btn-secondary btn-sm w-100" disabled>
                <i class="bi bi-x-circle me-1"></i> Hết hàng
            </button>
        @endif
    </div>
</div>
