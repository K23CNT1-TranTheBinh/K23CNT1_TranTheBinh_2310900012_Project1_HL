<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model DanhGia - danh gia san pham
class DanhGia extends Model
{
    protected $table = "reviews";

    public $timestamps = true;

    // Bang reviews chi co created_at, khong co updated_at.
    const UPDATED_AT = null;

    protected $fillable = [
        "product_id",
        "user_id",
        "rating",
        "comment",
        "status",
    ];

    // Thuoc ve san pham
    public function product()
    {
        return $this->belongsTo(SanPham::class, "product_id");
    }

    // Thuoc ve khach hang
    public function customer()
    {
        return $this->belongsTo(KhachHang::class, "user_id");
    }
}
