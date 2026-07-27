<?php

namespace App\Http\Controllers;

use App\Models\QuanTriVien;
use App\Models\ThuongHieu;
use App\Models\DanhMuc;
use App\Models\MaGiamGia;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// TTB_QuanTriController - tong quan va dang xuat khu quan tri
// Luu y: Dang nhap dung chung voi customer o trang /dang-nhap (XacThucController).
//        Khi nhap dung tai khoan admin -> tu dong chuyen sang trang admin.
class TTB_QuanTriController extends BoDieuKhien
{
    // Dang xuat admin - chuyen ve trang dang nhap chung
    public function logout(Request $request)
    {
        Auth::guard("admin")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route("login")
            ->with("success", "Ban da dang xuat khoi quan tri.");
    }

    // Trang quan tri chinh - thong ke + doanh thu + don gan day + ban chay + ton kho thap
    public function dashboard()
    {
        // So lieu tong quan dung truc tiep boi cac the thong ke trong dashboard.
        $counts = [
            "products" => SanPham::count(),
            "orders" => DonHang::count(),
            "customers" => KhachHang::count(),
            "reviews" => DanhGia::count(),
            "coupons" => MaGiamGia::count(),
        ];

        // Chi ghi nhan doanh thu khi don da hoan thanh.
        $revenue = DonHang::where(
            "status",
            DonHang::STATUS_COMPLETED,
        )->sum("final_amount");

        // Doanh thu theo ngay (7 ngay gan nhat).
        // Alias date/value phai khop voi phan Chart.js trong Blade.
        $revenueByDay = DonHang::where(
            "status",
            DonHang::STATUS_COMPLETED,
        )
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("SUM(final_amount) as value"),
            )
            ->where("created_at", ">=", now()->subDays(6)->startOfDay())
            ->groupBy("date")
            ->orderBy("date")
            ->get();

        // Don hang gan day (6 don)
        $recentOrders = DonHang::with("customer")->latest()->take(6)->get();

        // San pham ban chay (5)
        $bestSellers = ChiTietDonHang::join(
            "orders",
            "orders.id",
            "=",
            "order_details.order_id",
        )
            ->whereIn("orders.status", [
                DonHang::STATUS_SHIPPING,
                DonHang::STATUS_COMPLETED,
            ])
            ->select(
                "order_details.product_id",
                "order_details.product_name",
                DB::raw("SUM(order_details.quantity) as total_sold"),
            )
            ->groupBy(
                "order_details.product_id",
                "order_details.product_name",
            )
            ->orderByDesc("total_sold")
            ->take(5)
            ->get();

        $maxSold = (int) ($bestSellers->max("total_sold") ?? 0);

        // San pham sap het hang (stock <= 10)
        $lowStock = SanPham::where("stock", "<=", 10)
            ->orderBy("stock", "asc")
            ->take(5)
            ->get();

        $statusMap = [
            DonHang::STATUS_PENDING => [
                "label" => "Chờ xác nhận",
                "class" => "bg-warning text-dark",
            ],
            DonHang::STATUS_CONFIRMED => [
                "label" => "Đã xác nhận",
                "class" => "bg-info text-dark",
            ],
            DonHang::STATUS_SHIPPING => [
                "label" => "Đang giao",
                "class" => "bg-primary",
            ],
            DonHang::STATUS_COMPLETED => [
                "label" => "Hoàn thành",
                "class" => "bg-success",
            ],
            DonHang::STATUS_CANCELLED => [
                "label" => "Đã huỷ",
                "class" => "bg-danger",
            ],
        ];

        $statusCounts = DonHang::select("status", DB::raw("COUNT(*) as total"))
            ->groupBy("status")
            ->pluck("total", "status");

        return view(
            "quan_tri.TTB_tong_quan",
            compact(
                "counts",
                "revenue",
                "revenueByDay",
                "recentOrders",
                "bestSellers",
                "maxSold",
                "lowStock",
                "statusMap",
                "statusCounts",
            ),
        );
    }
}
