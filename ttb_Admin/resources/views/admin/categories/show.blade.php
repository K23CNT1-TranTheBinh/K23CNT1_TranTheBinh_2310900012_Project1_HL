@extends('admin.layouts.master')


@section('content')


<div class="container-fluid">


    <div class="card shadow">


        <div class="card-header bg-info text-white">

            <h4 class="mb-0">
                Chi tiết danh mục
            </h4>

        </div>



        <div class="card-body">


            <table class="table table-bordered">


                <tr>

                    <th width="200">
                        ID
                    </th>

                    <td>
                        {{$category->id}}
                    </td>

                </tr>



                <tr>

                    <th>
                        Tên danh mục
                    </th>

                    <td>
                        {{$category->name}}
                    </td>

                </tr>



                <tr>

                    <th>
                        Slug
                    </th>

                    <td>
                        {{$category->slug}}
                    </td>

                </tr>



                <tr>

                    <th>
                        Mô tả
                    </th>

                    <td>
                        {{$category->description}}
                    </td>

                </tr>



                <tr>

                    <th>
                        Trạng thái
                    </th>

                    <td>

                        @if($category->status)

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
                        {{$category->created_at}}
                    </td>

                </tr>


            </table>



            <a href="/admin/categories/{{$category->id}}/edit"
            class="btn btn-warning">

                Sửa

            </a>



            <a href="/admin/categories"
            class="btn btn-secondary">

                Quay lại

            </a>


        </div>


    </div>


</div>


@endsection