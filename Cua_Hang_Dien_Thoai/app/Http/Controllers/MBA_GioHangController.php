<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// MBA_GioHangController - gio hang (yeu cau dang nhap customer)
class MBA_GioHangController extends BoDieuKhien
{
    // Hien thi gio hang + tong tam tinh
    public function index()
    {
        $customerId = Auth::guard("customer")->id();

        $items = GioHang::with("product")
            ->where("user_id", $customerId)
            ->latest()
            ->get();

        $subtotal = $items->sum(function ($item) {
            return $item->product
                ? $item->product->current_price * $item->quantity
                : 0;
        });

        return view("nguoi_dung.MBA_gio_hang", compact("items", "subtotal"));
    }

    // Them san pham vao gio (upsert neu da co)
    public function add(Request $request)
    {
        $data = $request->validate([
            "product_id" => ["required", "integer", "exists:products,id"],
            "quantity" => ["required", "integer", "min:1", "max:99"],
        ]);

        $customerId = Auth::guard("customer")->id();
        try {
            $product = DB::transaction(function () use ($data, $customerId) {
                $product = SanPham::where("id", $data["product_id"])
                    ->where("status", 1)
                    ->lockForUpdate()
                    ->first();

                if (!$product || (int) $product->stock < 1) {
                    throw new \RuntimeException(
                        "San pham khong ton tai hoac da het hang.",
                    );
                }

                $cart = GioHang::where("user_id", $customerId)
                    ->where("product_id", $data["product_id"])
                    ->lockForUpdate()
                    ->first();

                $newQty =
                    (int) $data["quantity"] +
                    ($cart ? (int) $cart->quantity : 0);
                if ($newQty > (int) $product->stock) {
                    throw new \RuntimeException(
                        "So luong vuot qua ton kho (" .
                            $product->stock .
                            " san pham).",
                    );
                }

                if ($cart) {
                    $cart->update(["quantity" => $newQty]);
                } else {
                    GioHang::create([
                        "user_id" => $customerId,
                        "product_id" => $data["product_id"],
                        "quantity" => (int) $data["quantity"],
                    ]);
                }

                return $product;
            }, 3);
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        }

        if ($request->boolean("buy_now")) {
            return redirect()
                ->route("checkout")
                ->with(
                    "success",
                    'Da them "' . $product->name . '" vao gio hang.',
                );
        }

        return back()->with(
            "success",
            'Da them "' . $product->name . '" vao gio hang.',
        );
    }

    // Cap nhat so luong (xoa neu <= 0)
    public function update(Request $request)
    {
        $data = $request->validate([
            "cart_id" => ["required", "integer"],
            "quantity" => ["required", "integer", "min:0", "max:99"],
        ]);

        $customerId = Auth::guard("customer")->id();

        try {
            DB::transaction(function () use ($data, $customerId) {
                $cart = GioHang::where("id", $data["cart_id"])
                    ->where("user_id", $customerId)
                    ->lockForUpdate()
                    ->first();

                if (!$cart) {
                    throw new \RuntimeException(
                        "Khong tim thay mat hang trong gio.",
                    );
                }

                if ((int) $data["quantity"] <= 0) {
                    $cart->delete();
                    return;
                }

                $product = SanPham::where("id", $cart->product_id)
                    ->where("status", 1)
                    ->lockForUpdate()
                    ->first();
                if (
                    !$product ||
                    (int) $data["quantity"] > (int) $product->stock
                ) {
                    throw new \RuntimeException(
                        "So luong vuot qua ton kho hoac san pham da ngung ban.",
                    );
                }

                $cart->update(["quantity" => (int) $data["quantity"]]);
            }, 3);
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        }

        if ((int) $data["quantity"] <= 0) {
            return back()->with("success", "Da xoa san pham khoi gio hang.");
        }
        return back()->with("success", "Da cap nhat so luong.");
    }

    // Xoa 1 dong trong gio
    public function remove($id)
    {
        $customerId = Auth::guard("customer")->id();

        $cart = GioHang::where("id", $id)
            ->where("user_id", $customerId)
            ->first();

        if (!$cart) {
            return back()->with("error", "Khong tim thay mat hang trong gio.");
        }

        $cart->delete();
        return back()->with("success", "Da xoa san pham khoi gio hang.");
    }
}
