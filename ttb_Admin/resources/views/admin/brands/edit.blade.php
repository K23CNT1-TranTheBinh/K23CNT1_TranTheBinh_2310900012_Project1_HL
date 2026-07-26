@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-warning">

            <h4 class="mb-0">
                Sửa thương hiệu
            </h4>

        </div>

        <div class="card-body">

            <form action="/admin/brands/{{$brand->id}}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Tên thương hiệu
                    </label>

                    <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name',$brand->name) }}">

                    @error('name')

                    <div class="text-danger">
                        {{ $message }}
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
                    value="{{ $brand->slug }}"
                    readonly>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Logo hiện tại
                    </label>

                    <br>

                    @if($brand->logo)

                        <img
                        src="{{ asset('uploads/brands/'.$brand->logo) }}"
                        width="120"
                        class="img-thumbnail">

                    @else

                        <span class="text-muted">
                            Chưa có logo
                        </span>

                    @endif

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Logo mới
                    </label>

                    <input
                    type="file"
                    name="logo"
                    class="form-control">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Trạng thái
                    </label>

                    <select
                    name="status"
                    class="form-select">

                        <option value="1"
                        {{$brand->status == 1 ? 'selected' : ''}}>
                            Hiển thị
                        </option>

                        <option value="0"
                        {{$brand->status == 0 ? 'selected' : ''}}>
                            Ẩn
                        </option>

                    </select>

                </div>

                <button class="btn btn-warning">

                    Cập nhật

                </button>

                <a href="/admin/brands"
                class="btn btn-secondary">

                    Quay lại

                </a>

            </form>

        </div>

    </div>

</div>

@endsection