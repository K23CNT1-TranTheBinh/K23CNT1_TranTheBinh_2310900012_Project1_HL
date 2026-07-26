@extends('admin.layouts.master')

@section('content')

<h2>Quản lý thương hiệu</h2>

<a href="/admin/brands/create"
class="btn btn-primary mb-3">

    Thêm thương hiệu

</a>

<input
id="search"
class="form-control mb-3"
placeholder="Tìm kiếm thương hiệu...">

@if(session('success'))

<div class="alert alert-success">

    {{ session('success') }}

</div>

@endif

<div id="brand-data">

<table class="table table-bordered">

<tr>

<th>ID</th>

<th>Logo</th>

<th>Tên</th>

<th>Slug</th>

<th>Status</th>

<th>Ngày tạo</th>

<th>Action</th>

</tr>

@foreach($brands as $item)

<tr class="brand-row">

<td>
    {{ $item->id }}
</td>

<td class="text-center align-middle">

@if($item->logo)

<div class="d-flex justify-content-center align-items-center"
     style="height:80px;">

    <img
    src="{{ asset('uploads/brands/'.$item->logo) }}"
    style="
        max-width:80px;
        max-height:80px;
        object-fit:contain;
    ">

</div>

@endif

</td>

<td>
    {{ $item->name }}
</td>

<td>
    {{ $item->slug }}
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

{{ $item->created_at }}

</td>

<td>

<div class="d-flex gap-2">

<a href="/admin/brands/{{ $item->id }}"
class="btn btn-info btn-sm">

    Xem

</a>

<a href="/admin/brands/{{ $item->id }}/edit"
class="btn btn-warning btn-sm">

    Sửa

</a>

<a href="/admin/brands/status/{{ $item->id }}"
class="btn btn-secondary btn-sm">

    Ẩn/Hiện

</a>

<form
action="/admin/brands/{{ $item->id }}"
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

{{ $brands->onEachSide(0)->links() }}

</div>

<script>

document.getElementById('search').addEventListener('keyup', function(){

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll('.brand-row');

    rows.forEach(function(row){

        let text = row.innerText.toLowerCase();

        if(text.includes(keyword)){

            row.style.display = "";

        }else{

            row.style.display = "none";

        }

    });

});

</script>

@endsection