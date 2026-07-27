<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

// TTB_QuanLySanPhamController - them, xem, sua va xoa san pham
class TTB_QuanLySanPhamController extends BoDieuKhien
{
    // Danh sach san pham (phan trang 15)
    public function index(Request $request)
    {
        $query = SanPham::with(["category", "brand"]);

        if ($request->filled("q")) {
            $q = trim($request->get("q"));
            $query->where(function ($sub) use ($q) {
                $sub->where("name", "like", "%" . $q . "%")->orWhere(
                    "slug",
                    "like",
                    "%" . $q . "%",
                );
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view("quan_tri.TTB_danh_sach_san_pham", compact("products"));
    }

    // Form them san pham
    public function create()
    {
        $categories = DanhMuc::where("status", 1)->get();
        $brands = ThuongHieu::where("status", 1)->get();

        return view(
            "quan_tri.TTB_bieu_mau_san_pham",
            compact("categories", "brands") + ["product" => null],
        );
    }

    // Luu san pham moi
    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        $data["slug"] = $this->uniqueSlug($this->slugify($data["name"]));
        $data["is_featured"] = $request->boolean("is_featured") ? 1 : 0;
        $data["is_new"] = $request->boolean("is_new", true) ? 1 : 0;
        $data["image"] = $this->storeImage($request);

        SanPham::create($data);

        return redirect()
            ->route("admin.products.index")
            ->with("success", 'Da them san pham "' . $data["name"] . '".');
    }

    // Form sua san pham
    public function edit($id)
    {
        $product = SanPham::findOrFail($id);

        $categories = DanhMuc::where("status", 1)
            ->orWhere("id", $product->category_id)
            ->get();
        $brands = ThuongHieu::where("status", 1)
            ->orWhere("id", $product->brand_id)
            ->get();

        return view(
            "quan_tri.TTB_bieu_mau_san_pham",
            compact("product", "categories", "brands"),
        );
    }

    // Cap nhat san pham
    public function update(Request $request, $id)
    {
        $product = SanPham::findOrFail($id);
        $data = $this->validateProduct($request, $id);

        $data["is_featured"] = $request->boolean("is_featured") ? 1 : 0;
        $data["is_new"] = $request->boolean("is_new") ? 1 : 0;
        $data["slug"] = $this->uniqueSlug(
            $this->slugify($data["name"]),
            $product->id,
        );
        $oldImage = $product->image;
        $data["image"] = $this->storeImage($request);

        $product->update($data);
        if ($data["image"] !== $oldImage) {
            $this->deleteLocalImage($oldImage);
        }

        return redirect()
            ->route("admin.products.index")
            ->with("success", 'Da cap nhat san pham "' . $product->name . '".');
    }

    // Xoa san pham
    public function destroy($id)
    {
        $product = SanPham::findOrFail($id);
        if ($product->orderDetails()->exists()) {
            return back()->with(
                "error",
                "San pham da phat sinh don hang. Hay chuyen sang trang thai an thay vi xoa.",
            );
        }

        $name = $product->name;
        $image = $product->image;
        $product->delete();
        $this->deleteLocalImage($image);

        return redirect()
            ->route("admin.products.index")
            ->with("success", 'Da xoa san pham "' . $name . '".');
    }

    // Validate du lieu san pham
    private function validateProduct(Request $request, $id = null): array
    {
        $rules = [
            "name" => ["required", "string", "max:255"],
            "category_id" => ["required", "integer", "exists:categories,id"],
            "brand_id" => ["required", "integer", "exists:brands,id"],
            "price" => ["required", "numeric", "min:0"],
            "stock" => ["required", "integer", "min:0"],
            "sale_price" => ["nullable", "numeric", "min:0", "lte:price"],
            "short_desc" => ["nullable", "string", "max:500"],
            "description" => ["nullable", "string"],
            "image" => ["nullable", "string", "max:255"],
            "image_file" => [
                "nullable",
                "image",
                "mimes:jpg,jpeg,png,webp",
                "max:4096",
            ],
            "specs" => ["nullable"],
            "images" => ["nullable"],
            "status" => ["required", "boolean"],
        ];

        $data = $request->validate($rules);

        // Xu ly specs: neu la JSON string thi giai ma thanh array, model cast se luu lai
        $specsInput = $request->input("specs");
        if (is_string($specsInput) && $specsInput !== "") {
            $decoded = json_decode($specsInput, true);
            if (!is_array($decoded)) {
                throw ValidationException::withMessages([
                    "specs" => "Thong so ky thuat phai la JSON hop le.",
                ]);
            }
            $data["specs"] = $decoded;
        } elseif (is_array($specsInput)) {
            $data["specs"] = $specsInput;
        } else {
            $data["specs"] = null;
        }

        // Xu ly images: nhan array tu form hoac JSON string
        $imagesInput = $request->input("images");
        if (is_array($imagesInput)) {
            $data["images"] = array_values(
                array_filter(array_map("trim", $imagesInput)),
            );
        } elseif (is_string($imagesInput) && $imagesInput !== "") {
            $decoded = json_decode($imagesInput, true);
            if (is_array($decoded)) {
                $data["images"] = array_values(array_filter($decoded));
            } else {
                $data["images"] = array_values(
                    array_filter(
                        array_map("trim", preg_split("/\\R/", $imagesInput)),
                    ),
                );
            }
        } else {
            $data["images"] = [];
        }

        if (isset($data["sale_price"]) && (float) $data["sale_price"] <= 0) {
            $data["sale_price"] = null;
        }
        unset($data["image_file"]);
        $data["status"] = $request->boolean("status") ? 1 : 0;

        return $data;
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== "" ? $slug : "san-pham";
        $candidate = $base;
        $suffix = 2;

        while (
            SanPham::where("slug", $candidate)
                ->when($ignoreId, fn($q) => $q->where("id", "!=", $ignoreId))
                ->exists()
        ) {
            $candidate = $base . "-" . $suffix++;
        }

        return $candidate;
    }

    private function storeImage(Request $request): ?string
    {
        if (!$request->hasFile("image_file")) {
            $value = trim((string) $request->input("image", ""));
            return $value !== "" ? $value : null;
        }

        $directory = public_path("uploads/products");
        File::ensureDirectoryExists($directory);

        $file = $request->file("image_file");
        $name =
            "product-" .
            now()->format("YmdHis") .
            "-" .
            bin2hex(random_bytes(4)) .
            "." .
            $file->extension();
        $file->move($directory, $name);

        return "uploads/products/" . $name;
    }

    private function deleteLocalImage(?string $image): void
    {
        $relative = str_replace("\\", "/", ltrim((string) $image, "/"));
        if (str_starts_with($relative, "public/")) {
            $relative = substr($relative, 7);
        }
        if (!str_starts_with($relative, "uploads/products/")) {
            return;
        }

        $path = public_path($relative);
        if (is_file($path)) {
            File::delete($path);
        }
    }
}
