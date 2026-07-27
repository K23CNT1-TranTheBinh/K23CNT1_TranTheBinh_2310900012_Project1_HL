<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\QuanTriVien;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

// MBA_TaiKhoanController - trang tai khoan khach hang (yeu cau dang nhap)
class MBA_TaiKhoanController extends BoDieuKhien
{
    // Hien thi thong tin tai khoan hien tai
    public function index()
    {
        $customer = Auth::guard("customer")->user();
        return view("nguoi_dung.MBA_tai_khoan", compact("customer"));
    }

    // Lich su don hang cua khach
    public function orders()
    {
        $customerId = Auth::guard("customer")->id();

        $orders = DonHang::where("user_id", $customerId)
            ->withCount("details")
            ->latest()
            ->paginate(10);

        return view("nguoi_dung.MBA_don_hang_cua_toi", compact("orders"));
    }

    // Chi tiet mot don hang cua khach
    public function orderDetail($id)
    {
        $customerId = Auth::guard("customer")->id();

        $order = DonHang::with("details.product")
            ->where("id", $id)
            ->where("user_id", $customerId)
            ->firstOrFail();

        return view("nguoi_dung.MBA_chi_tiet_don_hang", compact("order"));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard("customer")->user();
        $data = $request->validate([
            "full_name" => ["required", "string", "max:100"],
            "email" => [
                "required",
                "email",
                "max:100",
                "unique:users,email," . $customer->id,
            ],
            "phone" => [
                "nullable",
                "string",
                "regex:/^(0|\\+84)[0-9]{9,10}$/",
            ],
            "address" => ["nullable", "string", "max:500"],
        ]);

        $normalizedEmail = mb_strtolower(trim($data["email"]));
        if (
            $normalizedEmail !== mb_strtolower((string) $customer->email) &&
            QuanTriVien::where("email", $normalizedEmail)->exists()
        ) {
            return back()
                ->withErrors([
                    "email" => "Email nay da duoc su dung trong he thong.",
                ])
                ->withInput();
        }

        $customer->update([
            "full_name" => trim($data["full_name"]),
            "email" => $normalizedEmail,
            "phone" => $data["phone"] ? trim($data["phone"]) : null,
            "address" => $data["address"] ? trim($data["address"]) : null,
        ]);

        return back()->with("success", "Da cap nhat thong tin tai khoan.");
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            "current_password" => ["required", "string"],
            "password" => [
                "required",
                "string",
                "min:8",
                "different:current_password",
                "confirmed",
            ],
        ]);

        $customer = Auth::guard("customer")->user();
        $stored = (string) $customer->password;
        if (preg_match("/^[a-f0-9]{32}$/i", $stored) === 1) {
            $matches = hash_equals(
                mb_strtolower($stored),
                md5($data["current_password"]),
            );
        } else {
            try {
                $matches = Hash::check($data["current_password"], $stored);
            } catch (\Throwable $e) {
                $matches = false;
            }
        }

        if (!$matches) {
            return back()->withErrors([
                "current_password" => "Mat khau hien tai khong dung.",
            ]);
        }

        $customer->password = Hash::make($data["password"]);
        $customer->save();

        $request->session()->regenerate();

        return back()->with("success", "Da doi mat khau thanh cong.");
    }

    public function cancelOrder($id)
    {
        $customerId = Auth::guard("customer")->id();

        try {
            DB::transaction(function () use ($id, $customerId) {
                $order = DonHang::with("details")
                    ->where("id", $id)
                    ->where("user_id", $customerId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($order->status !== DonHang::STATUS_PENDING) {
                    throw new \RuntimeException(
                        "Chi don dang cho xac nhan moi co the huy.",
                    );
                }

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

                $order->update(["status" => DonHang::STATUS_CANCELLED]);
            }, 3);
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        }

        return back()->with("success", "Da huy don hang va hoan lai ton kho.");
    }
}
