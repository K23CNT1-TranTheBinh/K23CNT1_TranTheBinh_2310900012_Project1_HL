<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use Illuminate\Http\Request;

// TTB_QuanLyDanhGiaController - duyet, an va xoa danh gia
class TTB_QuanLyDanhGiaController extends BoDieuKhien
{
    // Danh sach danh gia (kem product + user, phan trang 15)
    public function index()
    {
        $reviews = DanhGia::with(["product", "customer"])
            ->latest()
            ->paginate(15);

        return view("quan_tri.TTB_danh_sach_danh_gia", compact("reviews"));
    }

    // Dao trang thai (1 -> 0, 0 -> 1)
    public function toggle($id)
    {
        $review = DanhGia::findOrFail($id);
        $newStatus = (int) $review->status === 1 ? 0 : 1;
        $review->update(["status" => $newStatus]);

        $msg = $newStatus === 1 ? "Da duyet danh gia." : "Da an danh gia.";
        return back()->with("success", $msg);
    }

    // Xoa danh gia
    public function destroy($id)
    {
        $review = DanhGia::findOrFail($id);
        $review->delete();

        return back()->with("success", "Da xoa danh gia.");
    }
}
