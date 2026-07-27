<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use Illuminate\Http\Request;

// TTB_QuanLyThuongHieuController - them, xem, sua va xoa thuong hieu
class TTB_QuanLyThuongHieuController extends BoDieuKhien
{
    // Danh sach thuong hieu
    public function index()
    {
        $brands = ThuongHieu::latest()->paginate(15);
        return view("quan_tri.TTB_danh_sach_thuong_hieu", compact("brands"));
    }

    // Form them thuong hieu
    public function create()
    {
        return view("quan_tri.TTB_bieu_mau_thuong_hieu", ["brand" => null]);
    }

    // Luu thuong hieu moi (slug tu sinh)
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "logo" => ["nullable", "string", "max:255"],
            "status" => ["required", "boolean"],
        ]);

        $data["slug"] = $this->slugify($data["name"]);
        $data["slug"] = $this->uniqueSlug(ThuongHieu::class, $data["slug"]);
        $data["status"] = $request->boolean("status") ? 1 : 0;

        ThuongHieu::create($data);

        return redirect()
            ->route("admin.brands.index")
            ->with("success", 'Da them thuong hieu "' . $data["name"] . '".');
    }

    // Form sua thuong hieu
    public function edit($id)
    {
        $brand = ThuongHieu::findOrFail($id);
        return view("quan_tri.TTB_bieu_mau_thuong_hieu", compact("brand"));
    }

    // Cap nhat thuong hieu
    public function update(Request $request, $id)
    {
        $brand = ThuongHieu::findOrFail($id);

        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "logo" => ["nullable", "string", "max:255"],
            "status" => ["required", "boolean"],
        ]);

        $data["slug"] = $this->slugify($data["name"]);
        if ($data["slug"] !== $brand->slug) {
            $data["slug"] = $this->uniqueSlug(
                ThuongHieu::class,
                $data["slug"],
                $id,
            );
        }
        $data["status"] = $request->boolean("status") ? 1 : 0;

        $brand->update($data);

        return redirect()
            ->route("admin.brands.index")
            ->with(
                "success",
                'Da cap nhat thuong hieu "' . $brand->name . '".',
            );
    }

    // Xoa thuong hieu
    public function destroy($id)
    {
        $brand = ThuongHieu::findOrFail($id);
        $name = $brand->name;
        $brand->delete();

        return redirect()
            ->route("admin.brands.index")
            ->with("success", 'Da xoa thuong hieu "' . $name . '".');
    }

    // Tao slug duy nhat
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
