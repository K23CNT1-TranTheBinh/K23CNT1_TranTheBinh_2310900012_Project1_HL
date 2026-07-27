<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use App\Models\DanhMuc;
use App\Models\SanPham;

// MBA_TrangChuController - trang chu storefront
class MBA_TrangChuController extends BoDieuKhien
{
    // Hien thi trang chu: SP noi bat, SP moi, danh muc, thuong hieu
    public function index()
    {
        $featured = SanPham::where("status", 1)
            ->where("is_featured", 1)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating")
            ->latest()
            ->take(4)
            ->get();

        $newest = SanPham::where("status", 1)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating")
            ->latest()
            ->take(10)
            ->get();

        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();

        return view(
            "nguoi_dung.MBA_trang_chu",
            compact("featured", "newest", "categories", "brands"),
        );
    }
}
