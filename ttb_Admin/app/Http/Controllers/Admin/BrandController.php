<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{

    // Danh sách thương hiệu
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $brands = Brand::when($keyword, function ($query) use ($keyword) {

            return $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('slug', 'like', '%' . $keyword . '%');

        })
        ->orderBy('id', 'ASC')
        ->paginate(5);

        // Tìm kiếm realtime AJAX
        if ($request->ajax()) {

            return view(
                'admin.brands.table',
                compact('brands')
            )->render();

        }

        return view(
            'admin.brands.index',
            compact('brands', 'keyword')
        );
    }

    // Hiển thị form thêm
    public function create()
    {
        return view('admin.brands.create');
    }

    // Lưu thương hiệu mới
    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

        ], [

            'name.required' => 'Tên thương hiệu không được để trống',

        ]);

        $logo = null;

        if ($request->hasFile('logo')) {

            $logoName = time() . '.' .
                $request->logo->extension();

            $request->logo->move(
                public_path('uploads/brands'),
                $logoName
            );

            $logo = $logoName;
        }

        Brand::create([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'logo' => $logo,

            'status' => $request->status ?? 1

        ]);

        return redirect('/admin/brands')
            ->with('success', 'Thêm thương hiệu thành công');
    }

    // Xem chi tiết
    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        return view(
            'admin.brands.show',
            compact('brand')
        );
    }

    // Hiển thị form sửa
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        return view(
            'admin.brands.edit',
            compact('brand')
        );
    }

    // Cập nhật thương hiệu
    public function update(Request $request, $id)
    {
        $request->validate([

            'name' => 'required'

        ], [

            'name.required' => 'Tên thương hiệu không được để trống'

        ]);

        $brand = Brand::findOrFail($id);

        $logo = $brand->logo;

        if ($request->hasFile('logo')) {

            $logoName = time() . '.' .
                $request->logo->extension();

            $request->logo->move(
                public_path('uploads/brands'),
                $logoName
            );

            $logo = $logoName;
        }

        $brand->update([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'logo' => $logo,

            'status' => $request->status ?? 1

        ]);

        return redirect('/admin/brands')
            ->with('success', 'Cập nhật thương hiệu thành công');
    }

    // Xóa thương hiệu
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        $brand->delete();

        return redirect('/admin/brands')
            ->with('success', 'Xóa thương hiệu thành công');
    }

    // Đổi trạng thái Hiện / Ẩn
    public function changeStatus($id)
    {
        $brand = Brand::findOrFail($id);

        $brand->status = !$brand->status;

        $brand->save();

        return back()
            ->with('success', 'Cập nhật trạng thái thành công');
    }
}