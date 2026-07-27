<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use App\Models\DonHang;
use Illuminate\Support\Facades\Auth;

// MBA_ChiTietSanPhamController - trang chi tiet san pham
class MBA_ChiTietSanPhamController extends BoDieuKhien
{
    // Hien thi chi tiet san pham theo slug
    public function show($slug)
    {
        $product = SanPham::where("slug", $slug)
            ->where("status", 1)
            ->with(["brand", "category", "reviews.customer"])
            ->firstOrFail();

        // Tang luot xem
        $product->increment("views");

        // San pham lien quan (cung thuong hieu, khac id)
        $related = SanPham::where("status", 1)
            ->where("brand_id", $product->brand_id)
            ->where("id", "!=", $product->id)
            ->latest()
            ->take(4)
            ->get();

        $canReview = false;
        $hasReviewed = false;
        if (Auth::guard("customer")->check()) {
            $customerId = Auth::guard("customer")->id();
            $hasReviewed = DanhGia::where("product_id", $product->id)
                ->where("user_id", $customerId)
                ->exists();
            $canReview = ChiTietDonHang::where("product_id", $product->id)
                ->whereHas("order", function ($query) use ($customerId) {
                    $query
                        ->where("user_id", $customerId)
                        ->where("status", DonHang::STATUS_COMPLETED);
                })
                ->exists();
        }

        return view(
            "nguoi_dung.MBA_chi_tiet_san_pham",
            compact("product", "related", "canReview", "hasReviewed"),
        );
    }
}
