<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

// Model MaGiamGia - ma giam gia
class MaGiamGia extends Model
{
    protected $table = "coupons";

    public $timestamps = false;

    protected $fillable = [
        "code",
        "discount_type",
        "discount_value",
        "min_order_amount",
        "start_date",
        "end_date",
        "usage_limit",
        "used_count",
        "status",
    ];

    protected $casts = [
        "start_date" => "datetime",
        "end_date" => "datetime",
    ];

    // Kiem tra ma giam gia con hop le khong
    public function isValid(float $orderAmount): bool
    {
        if ((int) $this->status !== 1) {
            return false;
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        if ($orderAmount < (float) $this->min_order_amount) {
            return false;
        }
        if (
            !empty($this->usage_limit) &&
            (int) $this->used_count >= (int) $this->usage_limit
        ) {
            return false;
        }

        return true;
    }

    // Tinh so tien giam
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->discount_type === "percent") {
            return round(
                ($orderAmount * ((float) $this->discount_value)) / 100,
            );
        }
        // fixed: tru mot so tien co dinh
        return (float) $this->discount_value;
    }
}
