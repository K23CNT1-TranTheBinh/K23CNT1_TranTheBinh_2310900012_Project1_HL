@extends('admin.layouts.master')

@section('content')

<h2>Thêm sản phẩm</h2>


<form action="/admin/products" method="POST" enctype="multipart/form-data">

@csrf


<div class="mb-3">

<label class="form-label">
Tên sản phẩm
</label>

<input 
type="text"
name="name"
class="form-control"
required>

</div>



<div class="mb-3">

<label class="form-label">
Danh mục
</label>

<select name="category_id" class="form-control">


@foreach($categories as $category)

<option value="{{$category->id}}">

{{$category->name}}

</option>

@endforeach


</select>

</div>




<div class="mb-3">

<label class="form-label">
Thương hiệu
</label>


<select name="brand_id" class="form-control">


@foreach($brands as $brand)

<option value="{{$brand->id}}">

{{$brand->name}}

</option>

@endforeach


</select>


</div>




<div class="mb-3">

<label>
Giá
</label>

<input
type="number"
name="price"
class="form-control"
required>


</div>




<div class="mb-3">

<label>
Tồn kho
</label>

<input
type="number"
name="stock"
class="form-control"
required>


</div>




<div class="mb-3">

<label>
Ảnh sản phẩm
</label>


<input
type="file"
name="image"
class="form-control">


</div>




<div class="mb-3">

<label>
Mô tả
</label>


<textarea
name="description"
class="form-control"
rows="5"></textarea>


</div>




<div class="mb-3">


<label>
Trạng thái
</label>


<select name="status" class="form-control">


<option value="1">
Hiện
</option>


<option value="0">
Ẩn
</option>


</select>


</div>




<button class="btn btn-primary">

Lưu sản phẩm

</button>



<a href="/admin/products"
class="btn btn-secondary">

Quay lại

</a>



</form>


@endsection