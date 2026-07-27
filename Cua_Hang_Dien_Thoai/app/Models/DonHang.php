<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model DonHang - don hang
class DonHang extends Model
{
    protected $table = "orders";

    public $timestamps = true;

    // Cac trang thai don hang
    const STATUS_PENDING = "pending";
    const STATUS_CONFIRMED = "confirmed";
    const STATUS_SHIPPING = "shipping";
    const STATUS_COMPLETED = "completed";
    const STATUS_CANCELLED = "cancelled";

    protected $fillable = [
        "user_id",
        "coupon_id",
        "order_code",
        "total_amount",
        "shipping_fee",
        "discount",
        "final_amount",
        "status",
        "payment_method",
        "payment_status",
        "shipping_address",
        "shipping_phone",
        "shipping_name",
        "note",
    ];

    // Thuoc ve khach hang
    public function customer()
    {
        return $this->belongsTo(KhachHang::class, "user_id");
    }

    // Cac dong chi tiet cua don
    public function details()
    {
        return $this->hasMany(ChiTietDonHang::class, "order_id");
    }

    public function coupon()
    {
        return $this->belongsTo(MaGiamGia::class, "coupon_id");
    }
}
