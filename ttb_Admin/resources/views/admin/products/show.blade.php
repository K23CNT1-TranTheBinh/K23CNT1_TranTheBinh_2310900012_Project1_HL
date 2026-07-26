@extends('admin.layouts.master')


@section('content')


<h2>Chi tiết sản phẩm</h2>


<div class="card">

<div class="card-body">



<h4>
{{$product->name}}
</h4>



@if($product->image)

<img
src="{{asset('uploads/products/'.$product->image)}}"
width="200">

@endif



<p>
<b>Danh mục:</b>

{{$product->category->name ?? ''}}

</p>




<p>
<b>Thương hiệu:</b>

{{$product->brand->name ?? ''}}

</p>




<p>
<b>Giá:</b>

{{number_format($product->price)}}

</p>




<p>
<b>Tồn kho:</b>

{{$product->stock}}

</p>




<p>

<b>Mô tả:</b>

{{$product->description}}

</p>



<p>

<b>Trạng thái:</b>


@if($product->status)

<span class="badge bg-success">
Hiện
</span>


@else


<span class="badge bg-danger">
Ẩn
</span>


@endif


</p>



<a href="/admin/products"
class="btn btn-secondary">

Quay lại

</a>



</div>

</div>



@endsection