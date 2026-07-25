@extends('admin.layouts.master')


@section('content')


<h2>Quản lý danh mục</h2>


<a href="/admin/categories/create"
class="btn btn-primary mb-3">

    Thêm danh mục

</a>



<input 
id="search"
class="form-control mb-3"
placeholder="Tìm kiếm danh mục...">



@if(session('success'))

<div class="alert alert-success">

    {{session('success')}}

</div>

@endif



<div id="category-data">


<table class="table table-bordered">


<tr>

<th>ID</th>

<th>Tên</th>

<th>Slug</th>

<th>Mô tả</th>

<th>Status</th>

<th>Ngày tạo</th>

<th>Action</th>

</tr>



@foreach($categories as $item)


<tr class="category-row">


<td>
    {{$item->id}}
</td>


<td>
    {{$item->name}}
</td>


<td>
    {{$item->slug}}
</td>


<td>
    {{$item->description}}
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

{{$item->created_at}}

</td>



<td>


<div class="d-flex gap-2">


<a href="/admin/categories/{{$item->id}}"
class="btn btn-info btn-sm">

    Xem

</a>



<a href="/admin/categories/{{$item->id}}/edit"
class="btn btn-warning btn-sm">

    Sửa

</a>



<a href="/admin/categories/status/{{$item->id}}"
class="btn btn-secondary btn-sm">

    Ẩn/Hiện

</a>



<form 
action="/admin/categories/{{$item->id}}"
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



{{$categories->links()}}


</div>





<script>


document.getElementById('search').addEventListener('keyup', function(){


    let keyword = this.value.toLowerCase();



    let rows = document.querySelectorAll('.category-row');



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