<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;

// TTB_QuanLyKhachHangController - danh sach, khoa va mo khoa khach hang
class TTB_QuanLyKhachHangController extends BoDieuKhien
{
    // Danh sach khach hang (dem so don, phan trang 15)
    public function index()
    {
        $customers = KhachHang::withCount("orders")->latest()->paginate(15);

        return view("quan_tri.TTB_danh_sach_khach_hang", compact("customers"));
    }

    // Dao trang thai (1 -> 0, 0 -> 1)
    public function toggle($id)
    {
        $customer = KhachHang::findOrFail($id);
        $newStatus = (int) $customer->status === 1 ? 0 : 1;
        $customer->update(["status" => $newStatus]);

        $msg =
            $newStatus === 1 ? "Da mo khoa khach hang." : "Da khoa khach hang.";
        return back()->with("success", $msg);
    }
}
