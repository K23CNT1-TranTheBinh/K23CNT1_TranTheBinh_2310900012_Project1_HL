@extends('admin.layouts.master')


@section('content')


<h2>
Quản lý khách hàng
</h2>


<input

id="search"

class="form-control mb-3"

placeholder="Tìm kiếm khách hàng..."

>



<table class="table table-bordered">


<tr>

<th>ID</th>

<th>Họ tên</th>

<th>Email</th>

<th>Số điện thoại</th>

<th>Địa chỉ</th>

<th>Trạng thái</th>

<th>Action</th>

</tr>



@foreach($users as $item)


<tr>


<td>
{{$item->id}}
</td>


<td>
{{$item->full_name}}
</td>


<td>
{{$item->email}}
</td>


<td>
{{$item->phone}}
</td>


<td>
{{$item->address}}
</td>


<td>


@if($item->status)

<span class="badge bg-success">
Hoạt động
</span>


@else

<span class="badge bg-danger">
Khóa
</span>


@endif


</td>



<td>


<a href="/admin/users/{{$item->id}}"

class="btn btn-info btn-sm">

Xem

</a>


<form

action="/admin/users/{{$item->id}}"

method="POST"

style="display:inline">


@csrf

@method('DELETE')


<button

class="btn btn-danger btn-sm"

onclick="return confirm('Bạn chắc chắn muốn xóa?')">

Xóa

</button>


</form>



</td>


</tr>


@endforeach


</table>


{{$users->links()}}


@endsection