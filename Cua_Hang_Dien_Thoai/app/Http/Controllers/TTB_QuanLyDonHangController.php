<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// TTB_QuanLyDonHangController - quan ly don hang
class TTB_QuanLyDonHangController extends BoDieuKhien
{
    // Danh sach don hang (filter status + search, phan trang 15)
    public function index(Request $request)
    {
        $query = DonHang::with("customer");

        if ($request->filled("status")) {
            $query->where("status", $request->get("status"));
        }

        if ($request->filled("q")) {
            $q = trim($request->get("q"));
            $query->where(function ($sub) use ($q) {
                $sub->where("order_code", "like", "%" . $q . "%")
                    ->orWhere("shipping_name", "like", "%" . $q . "%")
                    ->orWhere("shipping_phone", "like", "%" . $q . "%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view("quan_tri.TTB_danh_sach_don_hang", compact("orders"));
    }

    // Chi tiet don hang
    public function show($id)
    {
        $order = DonHang::with(["customer", "details.product"])->findOrFail(
            $id,
        );

        return view("quan_tri.TTB_chi_tiet_don_hang", compact("order"));
    }

    // Cap nhat trang thai don hang
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            "status" => [
                "required",
                "in:pending,confirmed,shipping,completed,cancelled",
            ],
        ]);

        try {
            $order = DB::transaction(function () use ($id, $data) {
                $order = DonHang::with("details")
                    ->lockForUpdate()
                    ->findOrFail($id);
                $current = $order->status;
                $next = $data["status"];

                if ($current === $next) {
                    return $order;
                }

                $allowed = [
                    DonHang::STATUS_PENDING => [
                        DonHang::STATUS_CONFIRMED,
                        DonHang::STATUS_CANCELLED,
                    ],
                    DonHang::STATUS_CONFIRMED => [
                        DonHang::STATUS_SHIPPING,
                        DonHang::STATUS_CANCELLED,
                    ],
                    DonHang::STATUS_SHIPPING => [
                        DonHang::STATUS_COMPLETED,
                    ],
                    DonHang::STATUS_COMPLETED => [],
                    DonHang::STATUS_CANCELLED => [],
                ];

                if (!in_array($next, $allowed[$current] ?? [], true)) {
                    throw new \RuntimeException(
                        "Khong the chuyen don tu trang thai hien tai sang trang thai da chon.",
                    );
                }

                if ($next === DonHang::STATUS_CANCELLED) {
                    foreach ($order->details as $detail) {
                        SanPham::where("id", $detail->product_id)->increment(
                            "stock",
                            (int) $detail->quantity,
                        );
                    }

                    if (
                        Schema::hasColumn("orders", "coupon_id") &&
                        $order->coupon_id
                    ) {
                        $order
                            ->coupon()
                            ->where("used_count", ">", 0)
                            ->decrement("used_count");
                    }
                }

                $changes = ["status" => $next];
                if ($next === DonHang::STATUS_COMPLETED) {
                    $changes["payment_status"] = "paid";
                }
                $order->update($changes);

                return $order;
            }, 3);
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        }

        return back()->with(
            "success",
            'Da cap nhat trang thai don "' . $order->order_code . '".',
        );
    }
}
