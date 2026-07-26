@extends('admin.layouts.master')


@section('content')


<div class="container-fluid">


<div class="card shadow">


<div class="card-header bg-warning">

<h4>
Sửa sản phẩm
</h4>

</div>



<div class="card-body">


<form 
action="/admin/products/{{$product->id}}"
method="POST"
enctype="multipart/form-data">


@csrf

@method('PUT')



<div class="mb-3">

<label>
Tên sản phẩm
</label>


<input
type="text"
name="name"
class="form-control"
value="{{old('name',$product->name)}}">


</div>





<div class="mb-3">

<label>
Danh mục
</label>


<select 
name="category_id"
class="form-select">


@foreach($categories as $category)


<option 
value="{{$category->id}}"

{{$product->category_id == $category->id ? 'selected':''}}

>

{{$category->name}}

</option>


@endforeach


</select>

</div>





<div class="mb-3">

<label>
Thương hiệu
</label>


<select 
name="brand_id"
class="form-select">


@foreach($brands as $brand)


<option 
value="{{$brand->id}}"

{{$product->brand_id == $brand->id ? 'selected':''}}

>

{{$brand->name}}

</option>


@endforeach


</select>

</div>





<div class="mb-3">

<label>
Giá gốc
</label>


<input
type="number"
name="price"
class="form-control"
value="{{$product->price}}">


</div>





<div class="mb-3">

<label>
Giá khuyến mãi
</label>


<input
type="number"
name="sale_price"
class="form-control"
value="{{$product->sale_price}}">


</div>





<div class="mb-3">

<label>
Số lượng tồn kho
</label>


<input
type="number"
name="stock"
class="form-control"
value="{{$product->stock}}">


</div>





<div class="mb-3">

<label>
Mô tả ngắn
</label>


<textarea
name="short_desc"
class="form-control">{{ $product->short_desc }}</textarea>


</div>





<div class="mb-3">

<label>
Mô tả chi tiết
</label>


<textarea
name="description"
rows="5"
class="form-control">{{ $product->description }}</textarea>


</div>





<div class="mb-3">


<label>
Ảnh hiện tại
</label>

<br>


@if($product->image)


<img 
src="{{asset('uploads/products/'.$product->image)}}"
width="100">


@endif


</div>





<div class="mb-3">

<label>
Đổi ảnh mới
</label>


<input
type="file"
name="image"
class="form-control">


</div>





<div class="mb-3">

<label>
Trạng thái
</label>


<select
name="status"
class="form-select">


<option value="1"
{{$product->status==1?'selected':''}}>

Hiển thị

</option>


<option value="0"
{{$product->status==0?'selected':''}}>

Ẩn

</option>


</select>


</div>





<button class="btn btn-warning">

Cập nhật

</button>



<a href="/admin/products"
class="btn btn-secondary">

Quay lại

</a>


</form>


</div>


</div>


</div>


@endsection