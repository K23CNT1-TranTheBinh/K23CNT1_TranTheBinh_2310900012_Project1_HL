<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\MaGiamGia;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

// MBA_ThanhToanController - thanh toan don hang (yeu cau dang nhap customer)
class MBA_ThanhToanController extends BoDieuKhien
{
    // Hien thi trang thanh toan; mien phi ship tu 500.000d, con lai 30.000d
    public function index()
    {
        $customerId = Auth::guard("customer")->id();

        $items = GioHang::with("product")
            ->where("user_id", $customerId)
            ->latest()
            ->get();

        if ($items->isEmpty()) {
            return redirect()
                ->route("cart")
                ->with(
                    "error",
                    "Gio hang trong. Vui long them san pham truoc khi thanh toan.",
                );
        }

        $subtotal = $items->sum(
            fn($i) => $i->product
                ? $i->product->current_price * $i->quantity
                : 0,
        );
        $shippingFee = $subtotal >= 500000 ? 0 : 30000;

        return view(
            "nguoi_dung.MBA_thanh_toan",
            compact("items", "subtotal", "shippingFee"),
        );
    }

    // Xu ly dat hang (DB transaction: tao don + chi tiet + giam ton kho + tang coupon + xoa gio)
    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            "shipping_name" => ["required", "string", "max:100"],
            "shipping_phone" => [
                "required",
                "string",
                "regex:/^(0|\\+84)[0-9]{9,10}$/",
            ],
            "shipping_address" => ["required", "string", "max:255"],
            "note" => ["nullable", "string", "max:500"],
            "payment_method" => ["required", "in:cod,banking,momo"],
            "coupon_code" => ["nullable", "string", "max:50"],
        ]);

        $customerId = Auth::guard("customer")->id();

        try {
            $hasStockTrigger = $this->databaseHasStockTrigger();
            $hasCouponColumn = Schema::hasColumn("orders", "coupon_id");

            $order = DB::transaction(function () use (
                $customerId,
                $data,
                $hasStockTrigger,
                $hasCouponColumn,
            ) {
                $items = GioHang::where("user_id", $customerId)
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException("Gio hang trong.");
                }

                $products = SanPham::whereIn(
                    "id",
                    $items->pluck("product_id"),
                )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy("id");

                $totalAmount = 0;
                foreach ($items as $item) {
                    $product = $products->get($item->product_id);
                    if (!$product || (int) $product->status !== 1) {
                        throw new \RuntimeException(
                            "Co san pham khong con duoc kinh doanh.",
                        );
                    }
                    if (
                        (int) $item->quantity < 1 ||
                        (int) $item->quantity > (int) $product->stock
                    ) {
                        throw new \RuntimeException(
                            'San pham "' . $product->name . '" khong du ton kho.',
                        );
                    }
                    $totalAmount +=
                        $product->current_price * (int) $item->quantity;
                }

                $coupon = null;
                $discount = 0;
                if (!empty($data["coupon_code"])) {
                    $coupon = MaGiamGia::where(
                        "code",
                        strtoupper(trim($data["coupon_code"])),
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$coupon || !$coupon->isValid((float) $totalAmount)) {
                        throw new \RuntimeException(
                            "Ma giam gia khong hop le, da het han hoac het luot.",
                        );
                    }

                    $discount = min(
                        (float) $totalAmount,
                        max(
                            0,
                            $coupon->calculateDiscount((float) $totalAmount),
                        ),
                    );
                }

                $shippingFee = $totalAmount >= 500000 ? 0 : 30000;
                $orderData = [
                    "user_id" => $customerId,
                    "order_code" => $this->generateOrderCode(),
                    "total_amount" => $totalAmount,
                    "shipping_fee" => $shippingFee,
                    "discount" => $discount,
                    "final_amount" =>
                        $totalAmount + $shippingFee - $discount,
                    "status" => DonHang::STATUS_PENDING,
                    "payment_method" => $data["payment_method"],
                    "payment_status" => "pending",
                    "shipping_name" => trim($data["shipping_name"]),
                    "shipping_phone" => trim($data["shipping_phone"]),
                    "shipping_address" => trim($data["shipping_address"]),
                    "note" => $data["note"] ?? null,
                ];
                if ($hasCouponColumn) {
                    $orderData["coupon_id"] = $coupon?->id;
                }

                $order = DonHang::create($orderData);

                foreach ($items as $item) {
                    $product = $products->get($item->product_id);
                    $quantity = (int) $item->quantity;
                    $unitPrice = $product->current_price;

                    ChiTietDonHang::create([
                        "order_id" => $order->id,
                        "product_id" => $product->id,
                        "product_name" => $product->name,
                        "product_price" => $unitPrice,
                        "quantity" => $quantity,
                        "total_price" => $unitPrice * $quantity,
                    ]);

                    // Tuong thich database cu: neu trigger cu con ton tai,
                    // trigger se tru kho. Database moi do Laravel tu quan ly.
                    if (!$hasStockTrigger) {
                        $affected = SanPham::where("id", $product->id)
                            ->where("stock", ">=", $quantity)
                            ->decrement("stock", $quantity);
                        if ($affected !== 1) {
                            throw new \RuntimeException(
                                'San pham "' .
                                    $product->name .
                                    '" vua het hang.',
                            );
                        }
                    }
                }

                if ($coupon) {
                    $coupon->increment("used_count");
                }

                GioHang::whereIn("id", $items->pluck("id"))->delete();

                return $order;
            }, 3);
        } catch (\RuntimeException $e) {
            Log::error("Dat hang that bai", [
                "customer_id" => $customerId,
                "message" => $e->getMessage(),
            ]);
            return back()
                ->with("error", $e->getMessage())
                ->withInput();
        } catch (\Throwable $e) {
            Log::error("Dat hang gap loi he thong", [
                "customer_id" => $customerId,
                "message" => $e->getMessage(),
            ]);
            return back()
                ->with(
                    "error",
                    "Khong the dat hang luc nay. Vui long thu lai sau.",
                )
                ->withInput();
        }

        return redirect()
            ->route("checkout.success", $order->id)
            ->with(
                "success",
                "Dat hang thanh cong! Ma don: " . $order->order_code,
            );
    }

    // Trang dat hang thanh cong
    public function success($id)
    {
        $customerId = Auth::guard("customer")->id();

        $order = DonHang::with("details.product")
            ->where("id", $id)
            ->where("user_id", $customerId)
            ->firstOrFail();

        return view("nguoi_dung.MBA_dat_hang_thanh_cong", compact("order"));
    }

    private function generateOrderCode(): string
    {
        do {
            $code =
                "ORD" .
                now()->format("ymd") .
                str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
        } while (DonHang::where("order_code", $code)->exists());

        return $code;
    }

    private function databaseHasStockTrigger(): bool
    {
        try {
            $result = DB::selectOne(
                "SELECT COUNT(*) AS total
                 FROM information_schema.TRIGGERS
                 WHERE TRIGGER_SCHEMA = DATABASE()
                   AND TRIGGER_NAME = 'trg_after_insert_order_detail'",
            );

            return (int) ($result->total ?? 0) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
