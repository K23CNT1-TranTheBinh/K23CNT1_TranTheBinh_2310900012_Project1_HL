@extends('admin.layouts.master')


@section('content')


<div class="container-fluid">


    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h4 class="mb-0">
                Thêm danh mục
            </h4>

        </div>


        <div class="card-body">


            <form action="/admin/categories" method="POST">

                @csrf



                <div class="mb-3">

                    <label class="form-label">
                        Tên danh mục
                    </label>

                    <input 
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Nhập tên danh mục"
                    value="{{ old('name') }}">


                    @error('name')

                    <div class="text-danger mt-1">
                        {{ $message }}
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
                    rows="4"
                    placeholder="Nhập mô tả danh mục">{{ old('description') }}</textarea>


                </div>





                <div class="mb-3">


                    <label class="form-label">
                        Trạng thái
                    </label>


                    <select 
                    name="status"
                    class="form-select">


                        <option value="1">
                            Hiển thị
                        </option>


                        <option value="0">
                            Ẩn
                        </option>


                    </select>


                </div>





                <div class="mt-4">


                    <button class="btn btn-primary">

                        <i class="bi bi-save"></i>
                        Lưu danh mục

                    </button>



                    <a href="/admin/categories"
                    class="btn btn-secondary">

                        Quay lại

                    </a>


                </div>



            </form>


        </div>


    </div>


</div>


@endsection