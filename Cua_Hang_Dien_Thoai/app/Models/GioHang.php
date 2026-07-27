<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model GioHang - dong gio hang
class GioHang extends Model
{
    protected $table = "carts";

    public $timestamps = true;

    protected $fillable = ["user_id", "product_id", "quantity"];

    // Thuoc ve khach hang
    public function customer()
    {
        return $this->belongsTo(KhachHang::class, "user_id");
    }

    // San pham tuong ung
    public function product()
    {
        return $this->belongsTo(SanPham::class, "product_id");
    }
}
