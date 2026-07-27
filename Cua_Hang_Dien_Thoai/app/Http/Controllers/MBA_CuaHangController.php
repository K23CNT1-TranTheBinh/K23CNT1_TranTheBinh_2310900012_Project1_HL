<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;

// MBA_CuaHangController - trang danh sach san pham (filter / sort / phan trang)
class MBA_CuaHangController extends BoDieuKhien
{
    // Danh sach tat ca san pham voi filter
    public function index(Request $request)
    {
        $query = SanPham::where("status", 1)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating");

        $this->applyFilters($query, $request);
        $this->applySort($query, $request);

        $products = $query->paginate(12)->withQueryString();

        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();
        $title = "Tat ca dien thoai";

        return view(
            "nguoi_dung.MBA_cua_hang",
            compact("products", "categories", "brands", "title"),
        );
    }

    // San pham theo danh muc
    public function byCategory(Request $request, $slug)
    {
        $category = DanhMuc::where("slug", $slug)
            ->where("status", 1)
            ->firstOrFail();

        $query = SanPham::where("status", 1)
            ->where("category_id", $category->id)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating");

        $this->applyFilters($query, $request);
        $this->applySort($query, $request);

        $products = $query->paginate(12)->withQueryString();

        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();
        $title = $category->name;

        return view(
            "nguoi_dung.MBA_cua_hang",
            compact("products", "categories", "brands", "title"),
        );
    }

    // San pham theo thuong hieu
    public function byBrand(Request $request, $slug)
    {
        $brand = ThuongHieu::where("slug", $slug)
            ->where("status", 1)
            ->firstOrFail();

        $query = SanPham::where("status", 1)
            ->where("brand_id", $brand->id)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating");

        $this->applyFilters($query, $request);
        $this->applySort($query, $request);

        $products = $query->paginate(12)->withQueryString();

        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();
        $title = $brand->name;

        return view(
            "nguoi_dung.MBA_cua_hang",
            compact("products", "categories", "brands", "title"),
        );
    }

    // Tim kiem san pham theo tu khoa
    public function search(Request $request)
    {
        $q = trim($request->get("q", ""));

        $query = SanPham::where("status", 1)
            ->with(["brand", "category"])
            ->withCount("reviews")
            ->withAvg("reviews", "rating");

        if ($q !== "") {
            $query->where(function ($sub) use ($q) {
                $sub->where("name", "like", "%" . $q . "%")
                    ->orWhere("short_desc", "like", "%" . $q . "%")
                    ->orWhere("description", "like", "%" . $q . "%");
            });
        }

        $this->applyFilters($query, $request);
        $this->applySort($query, $request);

        $products = $query->paginate(12)->withQueryString();

        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();
        $title = "Ket qua tim kiem" . ($q !== "" ? ': "' . $q . '"' : "");

        return view(
            "nguoi_dung.MBA_cua_hang",
            compact("products", "categories", "brands", "title"),
        );
    }

    // Ap dung bo loc category / brand / khoang gia
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled("category")) {
            $categoryId = filter_var(
                $request->get("category"),
                FILTER_VALIDATE_INT,
            );
            if ($categoryId) {
                $query->where("category_id", $categoryId);
            }
        }

        if ($request->filled("brand")) {
            $brands = (array) $request->get("brand");
            $brandIds = array_values(
                array_filter(
                    array_map(
                        fn($id) => filter_var($id, FILTER_VALIDATE_INT),
                        $brands,
                    ),
                ),
            );
            if ($brandIds) {
                $query->whereIn("brand_id", $brandIds);
            }
        }

        $priceSql =
            "(CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price ELSE price END)";
        $minPrice = filter_var(
            $request->get("min_price"),
            FILTER_VALIDATE_FLOAT,
        );
        $maxPrice = filter_var(
            $request->get("max_price"),
            FILTER_VALIDATE_FLOAT,
        );
        if ($minPrice !== false && $minPrice >= 0) {
            $query->whereRaw($priceSql . " >= ?", [$minPrice]);
        }
        if ($maxPrice !== false && $maxPrice >= 0) {
            $query->whereRaw($priceSql . " <= ?", [$maxPrice]);
        }
    }

    // Ap dung sap xep
    private function applySort($query, Request $request): void
    {
        $sort = $request->get("sort", "newest");
        switch ($sort) {
            case "price_asc":
                $query->orderByRaw(
                    "(CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price ELSE price END) ASC",
                );
                break;
            case "price_desc":
                $query->orderByRaw(
                    "(CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price ELSE price END) DESC",
                );
                break;
            case "name_asc":
                $query->orderBy("name", "asc");
                break;
            case "popular":
                $query->orderBy("views", "desc");
                break;
            case "newest":
            default:
                $query->latest();
                break;
        }
    }
}
