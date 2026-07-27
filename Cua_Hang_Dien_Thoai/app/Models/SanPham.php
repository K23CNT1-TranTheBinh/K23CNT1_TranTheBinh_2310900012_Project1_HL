<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model SanPham - san pham dien thoai
class SanPham extends Model
{
    protected $table = "products";

    public $timestamps = true;

    protected $fillable = [
        "name",
        "slug",
        "category_id",
        "brand_id",
        "price",
        "sale_price",
        "stock",
        "description",
        "short_desc",
        "image",
        "images",
        "specs",
        "is_featured",
        "is_new",
        "views",
        "status",
    ];

    protected $casts = [
        "images" => "array",
        "specs" => "array",
        "price" => "float",
        "sale_price" => "float",
    ];

    // Thuoc danh muc
    public function category()
    {
        return $this->belongsTo(DanhMuc::class, "category_id");
    }

    // Thuoc thuong hieu
    public function brand()
    {
        return $this->belongsTo(ThuongHieu::class, "brand_id");
    }

    // Danh gia (chi lay status=1, moi nhat)
    public function reviews()
    {
        return $this->hasMany(DanhGia::class, "product_id")
            ->where("status", 1)
            ->latest();
    }

    // Tat ca danh gia (khi admin can xem ca an)
    public function allReviews()
    {
        return $this->hasMany(DanhGia::class, "product_id");
    }

    // Cac dong trong gio hang
    public function carts()
    {
        return $this->hasMany(GioHang::class, "product_id");
    }

    // Chi tiet don hang chua san pham nay
    public function orderDetails()
    {
        return $this->hasMany(ChiTietDonHang::class, "product_id");
    }

    // Gia hien tai (sale_price neu < price, nguoc lai price)
    public function getCurrentPriceAttribute(): float
    {
        if (
            !empty($this->sale_price) &&
            $this->sale_price > 0 &&
            $this->sale_price < $this->price
        ) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    // Phan tram giam gia (int)
    public function getDiscountPercentAttribute(): int
    {
        if (
            !empty($this->sale_price) &&
            $this->sale_price > 0 &&
            $this->sale_price < $this->price &&
            $this->price > 0
        ) {
            return (int) round((1 - $this->sale_price / $this->price) * 100);
        }
        return 0;
    }

    // Diem danh gia trung binh (1 so thap phan)
    public function getAvgRatingAttribute(): float
    {
        if (array_key_exists("reviews_avg_rating", $this->attributes)) {
            $value = $this->attributes["reviews_avg_rating"];
            return $value ? round((float) $value, 1) : 0.0;
        }

        $avg = $this->reviews()->avg("rating");
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    // Đường dẫn ảnh hoàn chỉnh cho cả URL từ xa và tên file lưu cục bộ.
    public function getImageUrlAttribute(): string
    {
        return $this->resolveImageUrl($this->image);
    }

    public function resolveImageUrl($value): string
    {
        $image = trim((string) $value);

        if ($image === "") {
            return asset("images/anh_dien_thoai_mac_dinh.svg");
        }

        if (
            filter_var($image, FILTER_VALIDATE_URL) ||
            str_starts_with($image, "data:")
        ) {
            return $image;
        }

        $relativePath = str_replace("\\", "/", ltrim($image, "/"));
        if (str_starts_with($relativePath, "public/")) {
            $relativePath = substr($relativePath, 7);
        }
        $candidates = [];

        if (
            str_starts_with($relativePath, "storage/") ||
            str_starts_with($relativePath, "images/") ||
            str_starts_with($relativePath, "uploads/")
        ) {
            $candidates[] = $relativePath;
        } else {
            $candidates[] = "uploads/products/" . $relativePath;
            $candidates[] = "storage/products/" . $relativePath;
            $candidates[] = "images/" . $relativePath;
        }

        foreach ($candidates as $candidate) {
            if (is_file(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return asset("images/anh_dien_thoai_mac_dinh.svg");
    }
}
