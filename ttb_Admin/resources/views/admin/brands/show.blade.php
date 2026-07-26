@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-info text-white">

            <h4 class="mb-0">
                Chi tiết thương hiệu
            </h4>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="200">
                        ID
                    </th>

                    <td>
                        {{$brand->id}}
                    </td>

                </tr>

                <tr>

                    <th>
                        Tên thương hiệu
                    </th>

                    <td>
                        {{$brand->name}}
                    </td>

                </tr>

                <tr>

                    <th>
                        Slug
                    </th>

                    <td>
                        {{$brand->slug}}
                    </td>

                </tr>

                <tr>

                    <th>
                        Logo
                    </th>

                    <td>

                        @if($brand->logo)

                            <img
                            src="{{ asset('uploads/brands/'.$brand->logo) }}"
                            width="150"
                            class="img-thumbnail">

                        @else

                            <span class="text-muted">
                                Chưa có logo
                            </span>

                        @endif

                    </td>

                </tr>

                <tr>

                    <th>
                        Trạng thái
                    </th>

                    <td>

                        @if($brand->status)

                            <span class="badge bg-success">
                                Hiện
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Ẩn
                            </span>

                        @endif

                    </td>

                </tr>

                <tr>

                    <th>
                        Ngày tạo
                    </th>

                    <td>
                        {{$brand->created_at}}
                    </td>

                </tr>

            </table>

            <a href="/admin/brands/{{$brand->id}}/edit"
            class="btn btn-warning">

                Sửa

            </a>

            <a href="/admin/brands"
            class="btn btn-secondary">

                Quay lại

            </a>

        </div>

    </div>

</div>

@endsection