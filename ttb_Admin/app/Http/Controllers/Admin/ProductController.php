<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductController extends Controller
{
    // Danh sách sản phẩm
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $products = Product::with(['category','brand'])
            ->when($keyword, function ($query) use ($keyword) {

                return $query->where(
                    'name',
                    'like',
                    '%' . $keyword . '%'
                );

            })
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view(
            'admin.products.index',
            compact(
                'products',
                'keyword'
            )
        );
    }

    // Hiển thị form thêm
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view(
            'admin.products.create',
            compact(
                'categories',
                'brands'
            )
        );
    }

    // Lưu sản phẩm
    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'price' => 'required'

        ], [

            'name.required' => 'Tên sản phẩm không được để trống',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'brand_id.required' => 'Vui lòng chọn thương hiệu',
            'price.required' => 'Vui lòng nhập giá'

        ]);

        $image = null;

        if ($request->hasFile('image')) {

            $imageName = time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/products'),
                $imageName
            );

            $image = $imageName;
        }

        Product::create([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'category_id' => $request->category_id,

            'brand_id' => $request->brand_id,

            'price' => $request->price,

            'sale_price' => $request->sale_price,

            'stock' => $request->stock,

            'short_desc' => $request->short_desc,

            'description' => $request->description,

            'image' => $image,

            'is_featured' => $request->is_featured ?? 0,

            'is_new' => $request->is_new ?? 1,

            'views' => 0,

            'status' => $request->status ?? 1

        ]);

        return redirect('/admin/products')
            ->with(
                'success',
                'Thêm sản phẩm thành công'
            );
    }

    // Chi tiết sản phẩm
    public function show($id)
    {
        $product = Product::with([
            'category',
            'brand'
        ])->findOrFail($id);

        return view(
            'admin.products.show',
            compact('product')
        );
    }

    // Form sửa
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $categories = Category::all();

        $brands = Brand::all();

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories',
                'brands'
            )
        );
    }

    // Cập nhật sản phẩm
    public function update(
        Request $request,
        $id
    )
    {
        $request->validate([

            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'price' => 'required'

        ]);

        $product = Product::findOrFail($id);

        $image = $product->image;

        if ($request->hasFile('image')) {

            $imageName = time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/products'),
                $imageName
            );

            $image = $imageName;
        }

        $product->update([

            'name' => $request->name,

            'slug' => Str::slug($request->name),

            'category_id' => $request->category_id,

            'brand_id' => $request->brand_id,

            'price' => $request->price,

            'sale_price' => $request->sale_price,

            'stock' => $request->stock,

            'short_desc' => $request->short_desc,

            'description' => $request->description,

            'image' => $image,

            'is_featured' => $request->is_featured ?? 0,

            'is_new' => $request->is_new ?? 1,

            'status' => $request->status ?? 1

        ]);

        return redirect('/admin/products')
            ->with(
                'success',
                'Cập nhật sản phẩm thành công'
            );
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect('/admin/products')
            ->with(
                'success',
                'Xóa sản phẩm thành công'
            );
    }

    // Đổi trạng thái
    public function changeStatus($id)
    {
        $product = Product::findOrFail($id);

        $product->status = !$product->status;

        $product->save();

        return back()
            ->with(
                'success',
                'Cập nhật trạng thái thành công'
            );
    }
}

