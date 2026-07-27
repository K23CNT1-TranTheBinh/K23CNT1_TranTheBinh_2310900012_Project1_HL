<?php

use App\Http\Controllers\XacThucController;
use App\Http\Controllers\MBA_TaiKhoanController;
use App\Http\Controllers\MBA_GioHangController;
use App\Http\Controllers\MBA_CuaHangController;
use App\Http\Controllers\MBA_ThanhToanController;
use App\Http\Controllers\MBA_TrangChuController;
use App\Http\Controllers\MBA_ChiTietSanPhamController;
use App\Http\Controllers\MBA_DanhGiaSanPhamController;
use App\Http\Controllers\TTB_QuanLyDanhGiaController;
use App\Http\Controllers\TTB_QuanLyDanhMucController;
use App\Http\Controllers\TTB_QuanLyDonHangController;
use App\Http\Controllers\TTB_QuanLyKhachHangController;
use App\Http\Controllers\TTB_QuanLyMaGiamGiaController;
use App\Http\Controllers\TTB_QuanLySanPhamController;
use App\Http\Controllers\TTB_QuanLyThuongHieuController;
use App\Http\Controllers\TTB_QuanTriController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Khu vực mua hàng - không cần đăng nhập
|--------------------------------------------------------------------------
*/
Route::get("/", [MBA_TrangChuController::class, "index"])->name("home");

// Cửa hàng
Route::get("/dien-thoai", [MBA_CuaHangController::class, "index"])->name(
    "catalog",
);
Route::get("/danh-muc/{slug}", [
    MBA_CuaHangController::class,
    "byCategory",
])->name("catalog.category");
Route::get("/thuong-hieu/{slug}", [
    MBA_CuaHangController::class,
    "byBrand",
])->name("catalog.brand");
Route::get("/tim-kiem", [MBA_CuaHangController::class, "search"])->name(
    "catalog.search",
);

// Chi tiet san pham
Route::get("/san-pham/{slug}", [
    MBA_ChiTietSanPhamController::class,
    "show",
])->name("product.show");

/*
|--------------------------------------------------------------------------
| Xác thực dùng chung cho khách hàng và quản trị viên
|--------------------------------------------------------------------------
*/
Route::get("/dang-nhap", [XacThucController::class, "showLogin"])->name(
    "login",
);
Route::post("/dang-nhap", [XacThucController::class, "login"])->middleware(
    "throttle:6,1",
);
Route::get("/dang-ky", [XacThucController::class, "showRegister"])->name(
    "register",
);
Route::post("/dang-ky", [XacThucController::class, "register"])->middleware(
    "throttle:4,1",
);
Route::post("/dang-xuat", [XacThucController::class, "logout"])->name("logout");

/*
|--------------------------------------------------------------------------
| Giỏ hàng, thanh toán, tài khoản và đánh giá (yêu cầu đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware("customer")->group(function () {
    // Gio hang
    Route::get("/gio-hang", [MBA_GioHangController::class, "index"])->name(
        "cart",
    );
    Route::post("/gio-hang/them", [MBA_GioHangController::class, "add"])->name(
        "cart.add",
    );
    Route::post("/gio-hang/cap-nhat", [
        MBA_GioHangController::class,
        "update",
    ])->name("cart.update");
    Route::post("/gio-hang/xoa/{id}", [
        MBA_GioHangController::class,
        "remove",
    ])->name("cart.remove");

    // Thanh toan
    Route::get("/thanh-toan", [MBA_ThanhToanController::class, "index"])->name(
        "checkout",
    );
    Route::post("/thanh-toan", [
        MBA_ThanhToanController::class,
        "placeOrder",
    ])->name("checkout.place");
    Route::get("/don-hang/thanh-cong/{id}", [
        MBA_ThanhToanController::class,
        "success",
    ])->name("checkout.success");

    // Tai khoan
    Route::get("/tai-khoan", [MBA_TaiKhoanController::class, "index"])->name(
        "account",
    );
    Route::put("/tai-khoan", [
        MBA_TaiKhoanController::class,
        "updateProfile",
    ])->name("account.update");
    Route::put("/tai-khoan/mat-khau", [
        MBA_TaiKhoanController::class,
        "updatePassword",
    ])->name("account.password");
    Route::get("/tai-khoan/don-hang", [
        MBA_TaiKhoanController::class,
        "orders",
    ])->name("account.orders");
    Route::get("/tai-khoan/don-hang/{id}", [
        MBA_TaiKhoanController::class,
        "orderDetail",
    ])->name("account.order.detail");
    Route::patch("/tai-khoan/don-hang/{id}/huy", [
        MBA_TaiKhoanController::class,
        "cancelOrder",
    ])->name("account.order.cancel");

    // Danh gia san pham
    Route::post("/san-pham/{id}/danh-gia", [
        MBA_DanhGiaSanPhamController::class,
        "store",
    ])->name("review.store");
});

/*
|--------------------------------------------------------------------------
| Khu quản trị - prefix admin, name admin.*
|--------------------------------------------------------------------------
*/
Route::prefix("admin")
    ->name("admin.")
    ->middleware("admin")
    ->group(function () {
        // Tổng quan và đăng xuất quản trị
        Route::get("/", [TTB_QuanTriController::class, "dashboard"])->name(
            "dashboard",
        );
        Route::post("/logout", [TTB_QuanTriController::class, "logout"])->name(
            "logout",
        );

        // Quan ly san pham
        Route::resource("products", TTB_QuanLySanPhamController::class)->except(
            ["show"],
        );

        // Quan ly don hang
        Route::get("/orders", [
            TTB_QuanLyDonHangController::class,
            "index",
        ])->name("orders.index");
        Route::get("/orders/{id}", [
            TTB_QuanLyDonHangController::class,
            "show",
        ])->name("orders.show");
        Route::patch("/orders/{id}/status", [
            TTB_QuanLyDonHangController::class,
            "updateStatus",
        ])->name("orders.updateStatus");

        // Quan ly danh muc / thuong hieu / ma giam gia
        Route::resource(
            "categories",
            TTB_QuanLyDanhMucController::class,
        )->except(["show"]);
        Route::resource(
            "brands",
            TTB_QuanLyThuongHieuController::class,
        )->except(["show"]);
        Route::resource(
            "coupons",
            TTB_QuanLyMaGiamGiaController::class,
        )->except(["show"]);

        // Quan ly danh gia
        Route::get("/reviews", [
            TTB_QuanLyDanhGiaController::class,
            "index",
        ])->name("reviews.index");
        Route::patch("/reviews/{id}/toggle", [
            TTB_QuanLyDanhGiaController::class,
            "toggle",
        ])->name("reviews.toggle");
        Route::delete("/reviews/{id}", [
            TTB_QuanLyDanhGiaController::class,
            "destroy",
        ])->name("reviews.destroy");

        // Quan ly khach hang
        Route::get("/customers", [
            TTB_QuanLyKhachHangController::class,
            "index",
        ])->name("customers.index");
        Route::patch("/customers/{id}/toggle", [
            TTB_QuanLyKhachHangController::class,
            "toggle",
        ])->name("customers.toggle");
    });
