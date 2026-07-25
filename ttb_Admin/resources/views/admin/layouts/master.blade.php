<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            margin:0;
        }

        .sidebar{
            width:250px;
            height:100vh;
            background:#212529;
            position:fixed;
            left:0;
            top:0;
        }

        .sidebar a{
            display:block;
            color:white;
            text-decoration:none;
            padding:15px;
        }

        .sidebar a:hover{
            background:#343a40;
        }

        .content{
            margin-left:250px;
        }

        .navbar-admin{
            background:#f8f9fa;
            padding:15px;
            border-bottom:1px solid #ddd;
        }

        .main-content{
            padding:20px;
        }

    </style>

</head>
<body>

<div class="sidebar">

    <h3 class="text-white text-center mt-3">
        ADMIN
    </h3>

    <hr class="text-white">

    <a href="/admin/dashboard">
        Dashboard
    </a>

    <a href="/admin/categories">
        Danh mục
    </a>

    <a href="#">
        Thương hiệu
    </a>

    <a href="#">
        Sản phẩm
    </a>

    <a href="#">
        Khách hàng
    </a>

    <a href="#">
        Đơn hàng
    </a>

    <a href="#">
        Đánh giá
    </a>

    <a href="/admin/logout">
        Đăng xuất
    </a>

</div>

<div class="content">

    <div class="navbar-admin">

        Xin chào:
        <b>{{ session('admin_name') }}</b>

    </div>

    <div class="main-content">

        @yield('content')

    </div>

</div>

</body>
</html>