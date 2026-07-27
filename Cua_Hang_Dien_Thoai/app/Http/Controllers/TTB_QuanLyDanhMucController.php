<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;

// TTB_QuanLyDanhMucController - them, xem, sua va xoa danh muc
class TTB_QuanLyDanhMucController extends BoDieuKhien
{
    // Danh sach danh muc
    public function index()
    {
        $categories = DanhMuc::latest()->paginate(15);
        return view("quan_tri.TTB_danh_sach_danh_muc", compact("categories"));
    }

    // Form them danh muc
    public function create()
    {
        return view("quan_tri.TTB_bieu_mau_danh_muc", ["category" => null]);
    }

    // Luu danh muc moi (slug tu sinh)
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "description" => ["nullable", "string"],
            "status" => ["required", "boolean"],
        ]);

        $data["slug"] = $this->slugify($data["name"]);
        $data["status"] = $request->boolean("status") ? 1 : 0;

        // Dam bao slug duy nhat
        $data["slug"] = $this->uniqueSlug(DanhMuc::class, $data["slug"]);

        DanhMuc::create($data);

        return redirect()
            ->route("admin.categories.index")
            ->with("success", 'Da them danh muc "' . $data["name"] . '".');
    }

    // Form sua danh muc
    public function edit($id)
    {
        $category = DanhMuc::findOrFail($id);
        return view("quan_tri.TTB_bieu_mau_danh_muc", compact("category"));
    }

    // Cap nhat danh muc
    public function update(Request $request, $id)
    {
        $category = DanhMuc::findOrFail($id);

        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "description" => ["nullable", "string"],
            "status" => ["required", "boolean"],
        ]);

        $data["slug"] = $this->slugify($data["name"]);
        if ($data["slug"] !== $category->slug) {
            $data["slug"] = $this->uniqueSlug(
                DanhMuc::class,
                $data["slug"],
                $id,
            );
        }
        $data["status"] = $request->boolean("status") ? 1 : 0;

        $category->update($data);

        return redirect()
            ->route("admin.categories.index")
            ->with(
                "success",
                'Da cap nhat danh muc "' . $category->name . '".',
            );
    }

    // Xoa danh muc
    public function destroy($id)
    {
        $category = DanhMuc::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return redirect()
            ->route("admin.categories.index")
            ->with("success", 'Da xoa danh muc "' . $name . '".');
    }

    // Tao slug duy nhat trong bang
    private function uniqueSlug(
        string $model,
        string $slug,
        int $ignoreId = null,
    ): string {
        $base = $slug;
        $i = 1;
        $query = $model::where("slug", $slug);
        if ($ignoreId) {
            $query->where("id", "!=", $ignoreId);
        }
        while ($query->exists()) {
            $slug = $base . "-" . $i++;
            $query = $model::where("slug", $slug);
            if ($ignoreId) {
                $query->where("id", "!=", $ignoreId);
            }
        }
        return $slug;
    }
}
