<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// MBA_DanhGiaSanPhamController - khach dang danh gia san pham (yeu cau dang nhap)
class MBA_DanhGiaSanPhamController extends BoDieuKhien
{
    // Luu danh gia moi (status = 1 hien thi ngay)
    public function store(Request $request, $productId)
    {
        $data = $request->validate([
            "rating" => ["required", "integer", "between:1,5"],
            "comment" => ["required", "string", "min:5", "max:500"],
        ]);

        $customerId = Auth::guard("customer")->id();
        $product = SanPham::where("id", $productId)
            ->where("status", 1)
            ->first();
        if (!$product) {
            return back()->with("error", "San pham khong ton tai.");
        }

        $hasPurchased = ChiTietDonHang::where("product_id", $productId)
            ->whereHas("order", function ($query) use ($customerId) {
                $query
                    ->where("user_id", $customerId)
                    ->where("status", DonHang::STATUS_COMPLETED);
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with(
                "error",
                "Ban chi co the danh gia san pham sau khi don hang hoan thanh.",
            );
        }

        // ( tuy chon: mot khach chi duoc review 1 lan / san pham )
        $exists = DanhGia::where("product_id", $productId)
            ->where("user_id", $customerId)
            ->exists();

        if ($exists) {
            return back()->with(
                "error",
                "Ban da danh gia san pham nay truoc do.",
            );
        }

        try {
            DanhGia::create([
                "product_id" => $productId,
                "user_id" => $customerId,
                "rating" => $data["rating"],
                "comment" => trim($data["comment"]),
                "status" => 1,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with(
                "error",
                "Ban da danh gia san pham nay truoc do.",
            );
        }

        return back()->with("success", "Cam on ban da danh gia san pham!");
    }
}
