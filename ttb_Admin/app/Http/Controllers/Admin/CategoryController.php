<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    // Danh sách danh mục
public function index(Request $request)
{
    $keyword = $request->keyword;


    $categories = Category::when($keyword, function($query) use ($keyword){

        return $query->where('name', 'like', '%' . $keyword . '%')
                     ->orWhere('slug', 'like', '%' . $keyword . '%');

    })
    ->orderBy('id', 'ASC')
    ->paginate(5);



    // Nếu là tìm kiếm realtime AJAX
    if($request->ajax()){

        return view(
            'admin.categories.table',
            compact('categories')
        )->render();

    }



    return view('admin.categories.index', compact(
        'categories',
        'keyword'
    ));
}



    // Hiển thị form thêm
    public function create()
    {
        return view('admin.categories.create');
    }



    // Lưu danh mục mới
    public function store(Request $request)
    {

        $request->validate([

            'name' => 'required',
            

        ],[

            'name.required' => 'Tên danh mục không được để trống',
            
        ]);



        Category::create([

            'name' => $request->name,

           'slug'=>Str::slug($request->name),

            'description' => $request->description,

            'status' => $request->status ?? 1

        ]);



        return redirect('/admin/categories')
            ->with('success','Thêm danh mục thành công');

    }



    // Xem chi tiết
    public function show($id)
    {

        $category = Category::findOrFail($id);


        return view(
            'admin.categories.show',
            compact('category')
        );

    }



    // Hiển thị form sửa
    public function edit($id)
    {

        $category = Category::findOrFail($id);


        return view(
            'admin.categories.edit',
            compact('category')
        );

    }



    // Cập nhật danh mục
public function update(Request $request, $id)
{

    $request->validate([

        'name' => 'required'

    ],[

        'name.required' => 'Tên danh mục không được để trống'

    ]);


    $category = Category::findOrFail($id);



    $category->update([

        'name' => $request->name,

        'slug' => Str::slug($request->name),

        'description' => $request->description,

        'status' => $request->status ?? 1

    ]);



    return redirect('/admin/categories')
        ->with('success','Cập nhật danh mục thành công');

}

    // Xóa danh mục
    public function destroy($id)
    {

        $category = Category::findOrFail($id);


        $category->delete();



        return redirect('/admin/categories')
            ->with('success','Xóa danh mục thành công');

    }



    // Đổi trạng thái Hiện / Ẩn
    public function changeStatus($id)
    {

        $category = Category::findOrFail($id);


        $category->status = !$category->status;


        $category->save();



        return back()
            ->with('success','Cập nhật trạng thái thành công');

    }

}