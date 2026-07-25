@extends('admin.layouts.master')


@section('content')


<div class="container-fluid">


    <div class="card shadow">


        <div class="card-header bg-warning">

            <h4 class="mb-0">
                Sửa danh mục
            </h4>

        </div>



        <div class="card-body">


            <form action="/admin/categories/{{$category->id}}" method="POST">


                @csrf

                @method('PUT')



                <div class="mb-3">

                    <label class="form-label">
                        Tên danh mục
                    </label>


                    <input 
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{old('name',$category->name)}}">


                    @error('name')

                    <div class="text-danger">
                        {{$message}}
                    </div>

                    @enderror


                </div>





                <div class="mb-3">

                    <label class="form-label">
                        Slug
                    </label>


                    <input 
                    type="text"
                    name="slug"
                    class="form-control"
                    value="{{$category->slug}}">


                    @error('slug')

                    <div class="text-danger">
                        {{$message}}
                    </div>

                    @enderror


                </div>





                <div class="mb-3">


                    <label class="form-label">
                        Mô tả
                    </label>


                    <textarea
                    name="description"
                    class="form-control"
                    rows="4">{{old('description',$category->description)}}</textarea>


                </div>





                <div class="mb-3">


                    <label class="form-label">
                        Trạng thái
                    </label>


                    <select 
                    name="status"
                    class="form-select">


                        <option value="1"
                        {{$category->status == 1 ? 'selected' : ''}}>
                            Hiển thị
                        </option>


                        <option value="0"
                        {{$category->status == 0 ? 'selected' : ''}}>
                            Ẩn
                        </option>


                    </select>


                </div>





                <button class="btn btn-warning">

                    Cập nhật

                </button>



                <a href="/admin/categories"
                class="btn btn-secondary">

                    Quay lại

                </a>


            </form>


        </div>


    </div>


</div>


@endsection