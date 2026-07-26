@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h4 class="mb-0">
                Thêm thương hiệu
            </h4>

        </div>

        <div class="card-body">

            <form action="/admin/brands"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Tên thương hiệu
                    </label>

                    <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Nhập tên thương hiệu"
                    value="{{ old('name') }}">

                    @error('name')

                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>

                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Logo
                    </label>

                    <input
                    type="file"
                    name="logo"
                    class="form-control">

                    @error('logo')

                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>

                    @enderror

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

                        Lưu thương hiệu

                    </button>

                    <a href="/admin/brands"
                    class="btn btn-secondary">

                        Quay lại

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection