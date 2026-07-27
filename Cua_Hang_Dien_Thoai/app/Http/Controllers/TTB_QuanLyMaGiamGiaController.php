<?php

namespace App\Http\Controllers;

use App\Models\MaGiamGia;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// TTB_QuanLyMaGiamGiaController - them, xem, sua va xoa ma giam gia
class TTB_QuanLyMaGiamGiaController extends BoDieuKhien
{
    // Danh sach ma giam gia
    public function index()
    {
        $coupons = MaGiamGia::latest("start_date")->paginate(15);
        return view("quan_tri.TTB_danh_sach_ma_giam_gia", compact("coupons"));
    }

    // Form them ma giam gia
    public function create()
    {
        return view("quan_tri.TTB_bieu_mau_ma_giam_gia", ["coupon" => null]);
    }

    // Luu ma giam gia moi (code hoa thuong, used_count=0, status=1)
    public function store(Request $request)
    {
        $request->merge([
            "code" => strtoupper(trim((string) $request->input("code"))),
        ]);
        $data = $request->validate([
            "code" => [
                "required",
                "string",
                "max:50",
                "regex:/^[A-Z0-9_-]+$/",
                "unique:coupons,code",
            ],
            "discount_type" => ["required", "in:percent,fixed"],
            "discount_value" => ["required", "numeric", "min:1"],
            "min_order_amount" => ["nullable", "numeric", "min:0"],
            "start_date" => ["required", "date"],
            "end_date" => ["required", "date", "after_or_equal:start_date"],
            "usage_limit" => ["required", "integer", "min:1"],
            "status" => ["required", "boolean"],
        ]);

        $data["code"] = strtoupper(trim($data["code"]));
        $this->validateDiscountValue($data);
        $data["used_count"] = 0;
        $data["status"] = $request->boolean("status") ? 1 : 0;
        $data["min_order_amount"] = $data["min_order_amount"] ?? 0;

        MaGiamGia::create($data);

        return redirect()
            ->route("admin.coupons.index")
            ->with("success", 'Da them ma giam gia "' . $data["code"] . '".');
    }

    // Form sua ma giam gia
    public function edit($id)
    {
        $coupon = MaGiamGia::findOrFail($id);
        return view("quan_tri.TTB_bieu_mau_ma_giam_gia", compact("coupon"));
    }

    // Cap nhat ma giam gia
    public function update(Request $request, $id)
    {
        $coupon = MaGiamGia::findOrFail($id);
        $request->merge([
            "code" => strtoupper(trim((string) $request->input("code"))),
        ]);

        $data = $request->validate([
            "code" => [
                "required",
                "string",
                "max:50",
                "regex:/^[A-Z0-9_-]+$/",
                "unique:coupons,code," . $id,
            ],
            "discount_type" => ["required", "in:percent,fixed"],
            "discount_value" => ["required", "numeric", "min:1"],
            "min_order_amount" => ["nullable", "numeric", "min:0"],
            "start_date" => ["required", "date"],
            "end_date" => ["required", "date", "after_or_equal:start_date"],
            "usage_limit" => ["required", "integer", "min:1"],
            "status" => ["required", "boolean"],
        ]);

        $data["code"] = strtoupper(trim($data["code"]));
        $this->validateDiscountValue($data);
        if ((int) $data["usage_limit"] < (int) $coupon->used_count) {
            throw ValidationException::withMessages([
                "usage_limit" =>
                    "Gioi han su dung khong duoc nho hon so luot da dung.",
            ]);
        }
        $data["min_order_amount"] = $data["min_order_amount"] ?? 0;
        $data["status"] = $request->boolean("status") ? 1 : 0;

        $coupon->update($data);

        return redirect()
            ->route("admin.coupons.index")
            ->with(
                "success",
                'Da cap nhat ma giam gia "' . $coupon->code . '".',
            );
    }

    // Xoa ma giam gia
    public function destroy($id)
    {
        $coupon = MaGiamGia::findOrFail($id);
        $code = $coupon->code;
        $coupon->delete();

        return redirect()
            ->route("admin.coupons.index")
            ->with("success", 'Da xoa ma giam gia "' . $code . '".');
    }

    private function validateDiscountValue(array $data): void
    {
        if (
            $data["discount_type"] === "percent" &&
            (float) $data["discount_value"] > 100
        ) {
            throw ValidationException::withMessages([
                "discount_value" => "Muc giam theo phan tram toi da la 100%.",
            ]);
        }
    }
}
