<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model ChiTietDonHang - chi tiet don hang
class ChiTietDonHang extends Model
{
    protected $table = "order_details";

    public $timestamps = false;

    protected $fillable = [
        "order_id",
        "product_id",
        "product_name",
        "product_price",
        "quantity",
        "total_price",
    ];

    // Thuoc ve don hang
    public function order()
    {
        return $this->belongsTo(DonHang::class, "order_id");
    }

    // San pham tuong ung
    public function product()
    {
        return $this->belongsTo(SanPham::class, "product_id");
    }
}
