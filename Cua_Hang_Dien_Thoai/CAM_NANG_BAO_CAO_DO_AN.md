# CẨM NANG BÁO CÁO ĐỒ ÁN PHONE SHOP

> File “phao cứu sinh” khi thuyết trình và trả lời câu hỏi.  
> Dự án: Website bán điện thoại — Nhóm 5 — K23CNT1  
> Người phụ trách phần User: Mai Bình An (`MBA_`)  
> Người phụ trách phần Admin: Trần Thế Bình (`TTB_`)  
> Người phụ trách cơ sở dữ liệu: Nguyễn Văn Lượng (`NVL_`)

---

## 1. Giới thiệu dự án trong 30 giây

Phone Shop là website bán điện thoại được xây dựng bằng **PHP 8.1,
Laravel 10 và MySQL**, tổ chức theo mô hình **MVC**.

Hệ thống có hai khu vực:

- **Người dùng:** xem, tìm kiếm và lọc sản phẩm; đăng ký, đăng nhập; quản lý
  giỏ hàng; đặt hàng; xem lịch sử đơn; đánh giá sản phẩm.
- **Quản trị:** xem dashboard; CRUD sản phẩm, danh mục, thương hiệu, mã giảm
  giá; quản lý đơn hàng, đánh giá và khách hàng.

Hai loại tài khoản dùng chung một trang đăng nhập nhưng được tách bằng hai
Laravel guard là `customer` và `admin`. Middleware ngăn khách hàng truy cập
trang quản trị và ngăn người chưa đăng nhập sử dụng giỏ hàng, thanh toán.

---

## 2. Kiến trúc và luồng xử lý

```text
Trình duyệt
    ↓ gửi HTTP Request
routes/web.php
    ↓ xác định URL, HTTP method và Controller
Middleware customer/admin
    ↓ kiểm tra quyền truy cập
Controller
    ↓ validate và xử lý nghiệp vụ
Model Eloquent
    ↓ đọc/ghi
MySQL
    ↓ trả dữ liệu
Controller → Blade View → HTML
    ↓
Trình duyệt hiển thị kết quả
```

### Mô hình MVC trong dự án

| Thành phần | Vai trò | Ví dụ thực tế |
| --- | --- | --- |
| Model | Đại diện bảng và truy vấn dữ liệu | `app/Models/SanPham.php` ánh xạ bảng `products` |
| View | Giao diện hiển thị | `resources/views/nguoi_dung/MBA_trang_chu.blade.php` |
| Controller | Nhận request, xử lý nghiệp vụ, gọi Model và View | `MBA_TrangChuController.php` |
| Route | Nối URL với Controller | `routes/web.php` |
| Middleware | Chặn request không đủ quyền | `KiemTraKhachHang.php`, `KiemTraQuanTri.php` |

### Vì sao giao diện có đuôi `.blade.php`?

Blade là **template engine chính thức của Laravel**, không phải công nghệ bị
lệch khỏi HTML/PHP. File Blade vẫn chứa HTML, CSS và JavaScript, nhưng có thêm
cú pháp hỗ trợ:

```blade
@extends('nguoi_dung.MBA_bo_cuc_nguoi_dung')

@foreach ($products as $product)
    <h3>{{ $product->name }}</h3>
@endforeach
```

Laravel biên dịch Blade thành PHP, chạy PHP ở máy chủ rồi gửi **HTML thuần**
cho trình duyệt. Không được đổi file Blade thành `.html`, vì các lệnh
`@extends`, `@foreach`, `@csrf` và `{{ ... }}` sẽ không còn hoạt động.

---

## 3. Cấu trúc file cần nhớ

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── MBA_*.php             Phần người dùng
│   │   ├── TTB_*.php             Phần quản trị
│   │   ├── XacThucController.php Đăng nhập, đăng ký
│   │   └── BoDieuKhien.php       Controller cơ sở
│   └── Middleware/
│       ├── KiemTraKhachHang.php
│       └── KiemTraQuanTri.php
└── Models/                       Các Model Eloquent

resources/views/
├── nguoi_dung/MBA_*.blade.php
├── quan_tri/TTB_*.blade.php
└── components/mba_the_san_pham.blade.php

routes/web.php                    Toàn bộ URL của website
config/auth.php                   Hai guard customer/admin
lang/vi/validation.php           Thông báo validation tiếng Việt
database/NVL_CoSoDuLieu_CuaHangDienThoai.sql
```

### Ý nghĩa tiền tố

| Tiền tố | Người thực hiện | Phần phụ trách |
| --- | --- | --- |
| `MBA_` | Mai Bình An | Chức năng và giao diện người dùng |
| `TTB_` | Trần Thế Bình | Chức năng và giao diện quản trị |
| `NVL_` | Nguyễn Văn Lượng | Cơ sở dữ liệu MySQL |

Các file dùng chung như Model, xác thực, Middleware và Route không nhất thiết
có tiền tố cá nhân.

---

## 4. Công nghệ sử dụng và bằng chứng trong source

| Công nghệ | Phiên bản/vai trò | Ví dụ trong dự án |
| --- | --- | --- |
| HTML5 | Xây dựng cấu trúc trang | Các file `.blade.php` |
| CSS3 | Giao diện, responsive, hiệu ứng | CSS trong hai file bố cục Blade |
| JavaScript | Tương tác phía trình duyệt | Đoạn `<script>` trong các Blade |
| Bootstrap | User 5.3.2, Admin 5.3.0 | CDN trong hai file bố cục |
| Bootstrap Icons | Biểu tượng giao diện | Các class `bi bi-*` |
| Laravel Blade | Template phía máy chủ | `@extends`, `@section`, `@foreach`, `{{ }}` |
| Chart.js 4.4 | Biểu đồ doanh thu Admin | `TTB_tong_quan.blade.php` |
| PHP | Yêu cầu từ PHP 8.1 | Controller, Model, Middleware |
| Laravel | Bản đang khóa: 10.50.2 | `composer.lock` |
| Eloquent ORM | Truy vấn MySQL bằng Model | `SanPham::where(...)`, `DonHang::create(...)` |
| MySQL | Lưu dữ liệu | Cấu hình `DB_CONNECTION=mysql` |
| Composer | Quản lý thư viện PHP | `composer.json`, thư mục `vendor` |
| XAMPP/phpMyAdmin | Chạy và quản lý MySQL cục bộ | Import file SQL, cấu hình `.env` |
| VS Code | Viết và quản lý source | Môi trường phát triển |

### Lưu ý về jQuery

Dự án có tải **jQuery 3.7.1** trong bố cục người dùng, nhưng source hiện tại
chưa có đoạn `$()`, `jQuery()` hoặc `.ready()` sử dụng nó. Khi báo cáo:

- Cách an toàn: không liệt kê jQuery là công nghệ chính.
- Nếu vẫn liệt kê: nói rõ “đã tích hợp thư viện nhưng chức năng hiện tại chủ
  yếu dùng JavaScript và Bootstrap”.

### Dự án có React không?

Không. Dòng `Download the React DevTools` từng xuất hiện là từ trang báo lỗi
Laravel Ignition trong chế độ debug, không phải React của Phone Shop.

---

## 5. Cơ sở dữ liệu và quan hệ chính

| Bảng | Model | Chức năng |
| --- | --- | --- |
| `admins` | `QuanTriVien` | Tài khoản quản trị |
| `users` | `KhachHang` | Tài khoản khách hàng |
| `products` | `SanPham` | Sản phẩm |
| `categories` | `DanhMuc` | Danh mục |
| `brands` | `ThuongHieu` | Thương hiệu |
| `carts` | `GioHang` | Dòng sản phẩm trong giỏ |
| `orders` | `DonHang` | Thông tin chung của đơn |
| `order_details` | `ChiTietDonHang` | Các sản phẩm của đơn |
| `reviews` | `DanhGia` | Đánh giá sản phẩm |
| `coupons` | `MaGiamGia` | Mã giảm giá |

### Quan hệ dễ bị hỏi

```text
DanhMuc       1 ─── n SanPham
ThuongHieu    1 ─── n SanPham
KhachHang     1 ─── n GioHang
KhachHang     1 ─── n DonHang
KhachHang     1 ─── n DanhGia
DonHang       1 ─── n ChiTietDonHang
SanPham       1 ─── n ChiTietDonHang
SanPham       1 ─── n DanhGia
```

Model giữ tên tiếng Việt để dễ đọc, còn `$table` giữ tên bảng tiếng Anh vì
đó là định danh kỹ thuật có sẵn trong MySQL.

---

## 6. CRUD là gì?

| Chữ | Ý nghĩa | HTTP thường dùng | Laravel |
| --- | --- | --- | --- |
| C | Create — thêm mới | POST | `create()`, `store()` |
| R | Read — xem dữ liệu | GET | `index()`, `show()` |
| U | Update — cập nhật | PUT/PATCH | `edit()`, `update()` |
| D | Delete — xoá | DELETE | `destroy()` |

`create()` và `edit()` chủ yếu mở biểu mẫu. `store()` và `update()` mới là
hàm nhận dữ liệu, validation và ghi vào MySQL.

---

## 7. CRUD khu vực quản trị

Tất cả route Admin nằm trong nhóm:

```php
Route::prefix("admin")
    ->name("admin.")
    ->middleware("admin")
```

Nghĩa là URL có tiền tố `/admin`, tên route có tiền tố `admin.` và chỉ tài
khoản vượt qua `KiemTraQuanTri` mới truy cập được.

### 7.1. Sản phẩm — CRUD đầy đủ

Controller: `TTB_QuanLySanPhamController.php`  
Model: `SanPham.php`  
View: `TTB_danh_sach_san_pham.blade.php`,
`TTB_bieu_mau_san_pham.blade.php`

| Thao tác | Method | Chức năng |
| --- | --- | --- |
| Danh sách | `index()` | Tìm kiếm và phân trang 15 sản phẩm |
| Mở form thêm | `create()` | Lấy danh mục và thương hiệu |
| Thêm | `store()` | Validate, tạo slug, gọi `SanPham::create()` |
| Mở form sửa | `edit($id)` | Gọi `SanPham::findOrFail($id)` |
| Cập nhật | `update($request, $id)` | Validate rồi `$product->update()` |
| Xoá | `destroy($id)` | Gọi `$product->delete()` |

Validation tiêu biểu:

```php
"category_id" => ["required", "integer", "exists:categories,id"],
"brand_id"    => ["required", "integer", "exists:brands,id"],
"price"       => ["required", "numeric", "min:0"],
"stock"       => ["required", "integer", "min:0"],
"status"      => ["required", "boolean"],
```

Checkbox trạng thái có một input ẩn gửi `0` và checkbox gửi `1`; vì vậy rule
`boolean` nhận đúng dữ liệu và không còn lỗi `validation.in`.

### 7.2. Danh mục — CRUD đầy đủ

- Controller: `TTB_QuanLyDanhMucController.php`.
- Các hàm: `index`, `create`, `store`, `edit`, `update`, `destroy`.
- Khi thêm/sửa, hệ thống tự tạo slug và kiểm tra slug duy nhất.

### 7.3. Thương hiệu — CRUD đầy đủ

- Controller: `TTB_QuanLyThuongHieuController.php`.
- Các hàm CRUD giống danh mục.
- Có hàm riêng `uniqueSlug()` để tránh trùng đường dẫn.

### 7.4. Mã giảm giá — CRUD đầy đủ

- Controller: `TTB_QuanLyMaGiamGiaController.php`.
- Kiểm tra loại giảm `percent/fixed`, giá trị giảm, ngày bắt đầu/kết thúc,
  số lần sử dụng và trạng thái.
- Model `MaGiamGia` có `isValid()` và `calculateDiscount()`.

### 7.5. Đơn hàng — Read và Update

- Controller: `TTB_QuanLyDonHangController.php`.
- `index()`: lọc trạng thái, tìm kiếm, phân trang.
- `show($id)`: xem khách hàng và chi tiết đơn.
- `updateStatus()`: cập nhật trạng thái.

Trạng thái:

```text
pending → confirmed → shipping → completed; đơn pending/confirmed có thể chuyển sang cancelled
                         └──────→ cancelled
```

Khi chuyển sang `completed`, hệ thống cập nhật `payment_status = paid`.
Admin không tự tạo/xoá đơn nên đây không phải CRUD đầy đủ.

### 7.6. Đánh giá — Read, Update trạng thái, Delete

- `index()`: xem đánh giá.
- `toggle()`: duyệt/ẩn bằng cách đảo `status` 0 ↔ 1.
- `destroy()`: xoá đánh giá.

### 7.7. Khách hàng — Read và khoá/mở khoá

- `index()`: danh sách và số đơn của khách.
- `toggle()`: đảo `status` để khoá hoặc mở tài khoản.
- Không xoá khách hàng nhằm tránh mất liên kết với đơn hàng.

### 7.8. Dashboard

`TTB_QuanTriController::dashboard()` thống kê:

- Tổng sản phẩm, đơn, khách hàng, đánh giá, mã giảm giá.
- Tổng doanh thu đơn `completed`.
- Doanh thu bảy ngày gần nhất.
- Sáu đơn gần đây.
- Năm sản phẩm bán chạy.
- Sản phẩm có tồn kho nhỏ hơn hoặc bằng 10.
- Số đơn theo từng trạng thái.

Chart.js nhận dữ liệu từ `$revenueByDay` để vẽ biểu đồ đường.

---

## 8. Cơ chế khu vực người dùng

### 8.1. Trang chủ và cửa hàng — Read

- `MBA_TrangChuController::index()` lấy sản phẩm nổi bật, sản phẩm mới, danh
  mục và thương hiệu.
- `MBA_CuaHangController` xử lý danh sách, lọc theo danh mục/thương hiệu,
  khoảng giá, sắp xếp và tìm kiếm.
- `MBA_ChiTietSanPhamController::show($slug)` lấy sản phẩm, tăng lượt xem và
  lấy sản phẩm liên quan.

### 8.2. Đăng ký, đăng nhập và đăng xuất

Controller dùng chung: `XacThucController.php`.

1. Form gửi email/tên đăng nhập và mật khẩu.
2. Controller validation dữ liệu.
3. Hệ thống thử tìm `KhachHang` theo email trước.
4. Nếu không phải khách, hệ thống tìm `QuanTriVien` theo email hoặc username.
5. Đăng nhập đúng guard tương ứng.
6. Session được `regenerate()` để giảm nguy cơ session fixation.
7. Khách chuyển về trang chủ; quản trị chuyển tới dashboard.

`config/auth.php` khai báo:

```php
"customer" => provider "customers" → Model KhachHang
"admin"    => provider "admins"    → Model QuanTriVien
```

### 8.3. Giỏ hàng — Create, Read, Update, Delete

Controller: `MBA_GioHangController.php`.

| Chức năng | Method |
| --- | --- |
| Xem giỏ | `index()` |
| Thêm sản phẩm | `add()` |
| Đổi số lượng | `update()` |
| Xoá dòng giỏ | `remove()` |

Giỏ hàng được lưu trong **bảng `carts` của MySQL**, không lưu trong session.
Session chỉ giữ trạng thái đăng nhập. Khi thêm sản phẩm đã tồn tại, hệ thống
tăng số lượng và luôn kiểm tra tồn kho.

### 8.4. Thanh toán và đặt hàng

Controller: `MBA_ThanhToanController.php`.

Luồng đặt hàng:

1. Middleware kiểm tra khách đã đăng nhập.
2. Validation tên, điện thoại, địa chỉ, phương thức thanh toán.
3. Lấy giỏ hàng và kiểm tra sản phẩm/tồn kho.
4. Kiểm tra mã giảm giá nếu có.
5. Tính:

```text
Thành tiền = Tạm tính + Phí giao hàng − Giảm giá
```

6. Mở `DB::beginTransaction()`.
7. Tạo `orders`.
8. Tạo từng dòng `order_details`.
9. Trừ tồn kho.
10. Tăng số lần sử dụng coupon.
11. Xoá giỏ hàng.
12. Thành công thì `commit()`, lỗi thì `rollBack()`.

Transaction đảm bảo các thao tác đặt hàng thành công cùng nhau hoặc huỷ toàn
bộ, tránh trường hợp đã tạo đơn nhưng chưa trừ kho.

### 8.5. Tài khoản và lịch sử đơn

`MBA_TaiKhoanController`:

- `index()`: hiển thị thông tin khách hiện tại.
- `updateProfile()`: cập nhật họ tên, email, số điện thoại và địa chỉ.
- `updatePassword()`: xác minh mật khẩu hiện tại rồi đổi sang mật khẩu bcrypt.
- `orders()`: lịch sử đơn, phân trang 10.
- `orderDetail($id)`: chỉ lấy đơn thuộc đúng khách đang đăng nhập.
- `cancelOrder($id)`: chỉ hủy đơn đang chờ xác nhận, hoàn tồn kho và lượt coupon.

Điều kiện `where("user_id", $customerId)` ngăn khách xem đơn của người khác.

### 8.6. Đánh giá

`MBA_DanhGiaSanPhamController::store()` validation điểm và nội dung, xác minh
khách đã có đơn hoàn thành chứa sản phẩm, rồi tạo đánh giá gắn với `product_id`
và `user_id`. Ràng buộc unique ngăn một khách đánh giá trùng một sản phẩm.

---

## 9. Bảo mật và kiểm tra dữ liệu

### Những gì dự án đã có

- Middleware tách quyền khách hàng/quản trị.
- Hai guard riêng trong `config/auth.php`.
- Validation phía máy chủ bằng `$request->validate()`.
- `@csrf` trong form chống giả mạo request.
- `@method("PUT")`, `@method("PATCH")`, `@method("DELETE")` mô phỏng các HTTP
  method mà form HTML không gửi trực tiếp được.
- Eloquent/query builder giúp bind dữ liệu, giảm nguy cơ SQL Injection.
- Session được regenerate sau đăng nhập.
- Transaction khi đặt hàng.
- Kiểm tra chủ sở hữu khi xem chi tiết đơn.
- Mật khẩu mới dùng bcrypt; dữ liệu mẫu MD5 cũ được tự nâng cấp sau lần đăng
  nhập đúng đầu tiên.
- Giới hạn tần suất đăng nhập/đăng ký và kiểm tra trạng thái khóa ở mỗi request.
- Khóa dòng sản phẩm/coupon khi đặt hàng để tránh vượt kho hoặc vượt lượt dùng.

### Hạn chế phải trả lời trung thực

1. Dữ liệu mẫu SQL ban đầu vẫn ghi MD5 để giữ các tài khoản demo dễ nhập,
   nhưng ứng dụng không tạo mật khẩu MD5 mới. Khi đăng nhập đúng, mật khẩu mẫu
   được tự đổi sang bcrypt.
2. Thanh toán `banking` và `momo` mới mô phỏng trạng thái, chưa tích hợp cổng
   thanh toán thật.
3. Ảnh sản phẩm hỗ trợ URL, tên file cũ và tải file trực tiếp từ màn hình
   Admin. File upload nằm trong `public/uploads/products`; nếu file bị thiếu,
   hệ thống dùng `public/images/anh_dien_thoai_mac_dinh.svg`.
4. Website là đồ án chạy cục bộ, chưa triển khai HTTPS, email xác nhận, quên
   mật khẩu hoặc kiểm thử tải lớn.

Không nên nói “hệ thống bảo mật tuyệt đối”. Nên nói “đã có các lớp bảo vệ cơ
bản của Laravel và có định hướng nâng cấp”.

---

## 10. Các câu hỏi dễ bị giảng viên hỏi

### “Tại sao dùng Laravel?”

Laravel cung cấp sẵn MVC, Route, Middleware, Validation, Authentication,
Session, Blade và Eloquent. Nhờ vậy code tách rõ trách nhiệm, dễ bảo trì hơn
PHP thuần.

### “Blade khác HTML thế nào?”

Blade vẫn tạo HTML nhưng chạy phía máy chủ, cho phép kế thừa layout, vòng lặp,
điều kiện, hiển thị dữ liệu và CSRF. Trình duyệt cuối cùng chỉ nhận HTML.

### “Route có nhiệm vụ gì?”

Route ánh xạ URL và HTTP method tới đúng Controller. Ví dụ POST thêm sản phẩm
gọi `store()`, PUT cập nhật gọi `update()`, DELETE gọi `destroy()`.

### “Middleware khác Controller thế nào?”

Middleware kiểm tra request trước khi vào Controller, ví dụ kiểm tra đã đăng
nhập và đúng vai trò. Controller xử lý nghiệp vụ sau khi request hợp lệ.

### “Eloquent ORM là gì?”

Eloquent ánh xạ một class Model với một bảng MySQL, cho phép truy vấn bằng PHP
như `SanPham::where(...)` thay vì viết toàn bộ SQL thủ công.

### “Tại sao cần validation?”

Validation chặn dữ liệu thiếu, sai kiểu, giá âm hoặc khoá ngoại không tồn tại
trước khi ghi MySQL. HTML `required` chỉ hỗ trợ giao diện; validation phía máy
chủ mới là lớp kiểm tra chính.

### “Tại sao cần `@csrf`?”

Laravel tạo token theo session. Request POST/PUT/PATCH/DELETE thiếu hoặc sai
token sẽ bị từ chối, giúp chống CSRF.

### “Tại sao đặt hàng phải dùng transaction?”

Một đơn gồm nhiều thao tác: tạo đơn, tạo chi tiết, trừ kho, dùng coupon và xoá
giỏ. Transaction bảo đảm hoặc tất cả thành công, hoặc tất cả được rollback.

### “Giỏ hàng lưu ở đâu?”

Giỏ hàng lưu trong bảng `carts` của MySQL theo `user_id`; session giữ trạng
thái đăng nhập.

### “Phân biệt authentication và authorization?”

- Authentication: xác định người dùng là ai — đăng nhập bằng guard.
- Authorization: xác định họ được vào đâu — Middleware `customer/admin`.

### “Tại sao có hai guard?”

Khách hàng nằm trong bảng `users`, quản trị nằm trong bảng `admins`. Hai guard
giữ phiên đăng nhập và Model riêng, tránh khách truy cập nhầm quyền Admin.

### “Dự án có dùng React không?”

Không. React DevTools xuất hiện từ giao diện debug Laravel Ignition, không
thuộc source ứng dụng.

### “Dự án có dùng jQuery không?”

Source có nhúng CDN jQuery nhưng chức năng hiện tại chưa gọi API jQuery; phần
tương tác đang dùng JavaScript, Bootstrap và Chart.js.

### “Tại sao tên Model tiếng Việt nhưng bảng tiếng Anh?”

Tên class/file Việt hoá để nhóm dễ tìm khi trình bày. Tên bảng/cột là định
danh kỹ thuật trong SQL gốc nên giữ nguyên để không phá dữ liệu và quan hệ.

### “HTTP 500 nghĩa là gì?”

Đó là lỗi phía máy chủ. Cần đọc `storage/logs/laravel.log`, tìm dòng
`local.ERROR` mới nhất; không đoán theo các cảnh báo React DevTools hoặc
Chrome extension trong Console.

---

## 11. Kịch bản demo 5–7 phút

1. Giới thiệu mục tiêu và công nghệ.
2. Mở trang chủ, danh sách và chi tiết sản phẩm.
3. Đăng ký/đăng nhập khách hàng.
4. Thêm sản phẩm vào giỏ, cập nhật số lượng.
5. Đặt hàng và mở lịch sử đơn.
6. Đăng xuất khách, đăng nhập tài khoản quản trị.
7. Mở dashboard và biểu đồ doanh thu.
8. Thêm hoặc sửa một sản phẩm.
9. Mở đơn vừa đặt và cập nhật trạng thái.
10. Kết luận về MVC, phân quyền và transaction.

### Dữ liệu nên chuẩn bị trước khi demo

- Một tài khoản khách hoạt động.
- Một tài khoản Admin hoạt động.
- Ít nhất một sản phẩm còn tồn kho.
- Một mã giảm giá còn hạn.
- Một đơn hàng mẫu.
- MySQL đã chạy và `.env` trỏ đúng `db_phone_shop`.

---

## 12. Lệnh cứu hộ trước khi báo cáo

Chạy trong thư mục dự án:

```powershell
composer dump-autoload
php artisan optimize:clear
php artisan serve
```

Kiểm tra route:

```powershell
php artisan route:list
```

Xem lỗi Laravel mới nhất:

```powershell
((Select-String -Path ".\storage\logs\laravel.log" -Pattern "local.ERROR" |
Select-Object -Last 1).Line -split ' \{"view"')[0]
```

### Phân biệt cảnh báo vô hại và lỗi thật

| Thông báo | Ý nghĩa |
| --- | --- |
| `Download the React DevTools` | Do trang debug, không phải lỗi Phone Shop |
| `Chrome Built-In AI LanguageDetector` | Do Chrome/extension |
| `favicon.ico 404` | Thiếu icon, không làm CRUD lỗi |
| `GET /admin 500` | Lỗi Laravel thật, phải đọc `laravel.log` |
| `validation.in` | Dữ liệu không thuộc tập rule `in:` hoặc ngôn ngữ chưa nạp |

---

## 13. Checklist ngay trước khi vào bảo vệ

- [ ] XAMPP MySQL đang chạy.
- [ ] Database là `db_phone_shop`.
- [ ] Chạy `composer dump-autoload`.
- [ ] Chạy `php artisan optimize:clear`.
- [ ] Trang chủ mở được.
- [ ] Đăng nhập khách và Admin đều đúng luồng.
- [ ] Thêm/sửa sản phẩm không báo validation.
- [ ] Giỏ hàng và đặt hàng hoạt động.
- [ ] Dashboard mở được.
- [ ] Chuẩn bị tài khoản demo và không sửa dữ liệu quan trọng phút cuối.
- [ ] Mở sẵn `routes/web.php`, Controller, Model, Blade và file cẩm nang này.

---

## 14. Câu chốt bài

“Dự án áp dụng Laravel MVC để tách Route, Controller, Model và Blade View;
Eloquent kết nối MySQL; Middleware cùng hai guard tách quyền khách hàng và
quản trị; validation và CSRF kiểm tra request; transaction đảm bảo tính toàn
vẹn khi đặt hàng. Hệ thống đã đáp ứng luồng bán hàng và quản trị cơ bản, đồng
thời còn hướng phát triển về bảo mật mật khẩu, upload ảnh và thanh toán thật.”
