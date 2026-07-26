@extends('admin.layouts.master')

@section('content')

<h2>Quản lý sản phẩm</h2>

<a href="/admin/products/create"
class="btn btn-primary mb-3">

    Thêm sản phẩm

</a>

<input
id="search"
class="form-control mb-3"
placeholder="Tìm kiếm sản phẩm...">
<script>

document.getElementById('search').addEventListener('keyup', function(){

    let keyword = this.value;


    fetch("/admin/products?keyword=" + keyword)

    .then(response => response.text())

    .then(data => {


        let html = new DOMParser()
        .parseFromString(data,'text/html')
        .querySelector('table')
        .innerHTML;


        document.querySelector('table').innerHTML = html;


    });


});


</script>
@if(session('success'))

<div class="alert alert-success">

    {{session('success')}}

</div>

@endif

<table class="table table-bordered">

    <tr>

        <th>ID</th>

        <th>Ảnh</th>

        <th>Tên sản phẩm</th>

        <th>Danh mục</th>

        <th>Thương hiệu</th>

        <th>Giá</th>

        <th>Tồn kho</th>

        <th>Trạng thái</th>

        <th>Action</th>

    </tr>

    @foreach($products as $item)

    <tr class="product-row">

        <td>
            {{$item->id}}
        </td>

        <td>

            @if($item->image)

            <img
            src="{{asset('uploads/products/'.$item->image)}}"
            width="60">

            @endif

        </td>

        <td>
            {{$item->name}}
        </td>

        <td>
            {{$item->category->name ?? ''}}
        </td>

        <td>
            {{$item->brand->name ?? ''}}
        </td>

        <td>
            {{number_format($item->price)}}
        </td>

        <td>
            {{$item->stock}}
        </td>

        <td>

            @if($item->status)

            <span class="badge bg-success">
                Hiện
            </span>

            @else

            <span class="badge bg-danger">
                Ẩn
            </span>

            @endif

        </td>

        <td>

            <div class="d-flex gap-2">

                <a href="/admin/products/{{$item->id}}"
                class="btn btn-info btn-sm">

                    Xem

                </a>

                <a href="/admin/products/{{$item->id}}/edit"
                class="btn btn-warning btn-sm">

                    Sửa

                </a>

                <a href="/admin/products/status/{{$item->id}}"
                class="btn btn-secondary btn-sm">

                    Ẩn/Hiện

                </a>

                <form
                action="/admin/products/{{$item->id}}"
                method="POST">

                    @csrf
                    @method('DELETE')

                    <button
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Bạn có chắc muốn xóa?')">

                        Xóa

                    </button>

                </form>

            </div>

        </td>

    </tr>

    @endforeach

</table>

{{$products->links()}}

@endsection