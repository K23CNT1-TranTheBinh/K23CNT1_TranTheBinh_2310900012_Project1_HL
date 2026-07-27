# Cửa hàng điện thoại — Laravel 10 và MySQL

Đây là đồ án Nhóm 5, lớp K23CNT1. Tên các file nghiệp vụ của cả khu vực người dùng và khu vực quản trị đã được chuyển sang tiếng Việt không dấu để dễ tìm, dễ trình bày và vẫn tương thích Composer PSR-4 trên Windows. Tiền tố phân công vẫn được giữ nguyên: `MBA_` cho phần người dùng, `TTB_` cho phần quản trị và `NVL_` cho cơ sở dữ liệu.

## Chạy dự án

1. Giải nén thư mục `Cua_Hang_Dien_Thoai`.
2. Mở thư mục bằng Visual Studio Code.
3. Khởi động MySQL trong XAMPP.
4. Nếu chưa có cơ sở dữ liệu, nhập file `database/NVL_CoSoDuLieu_CuaHangDienThoai.sql` bằng phpMyAdmin. Nếu đang dùng database từ bản ZIP cũ, không cần xóa dữ liệu: chỉ chạy thêm `database/NVL_CapNhat_ChucNang.sql` đúng một lần.
5. Chạy lần lượt:

```powershell
composer install
composer dump-autoload
php artisan optimize:clear
php artisan serve
```

6. Mở `http://127.0.0.1:8000`.
7. Đăng nhập quản trị tại `http://127.0.0.1:8000/dang-nhap`; tài khoản quản trị hợp lệ sẽ tự chuyển vào khu quản trị.

## Các chức năng đã được hoàn thiện

- Đăng ký/đăng nhập riêng bằng guard `customer` và `admin`; mật khẩu mới dùng bcrypt, tài khoản MD5 mẫu được tự nâng cấp sau lần đăng nhập thành công.
- Giỏ hàng kiểm tra tồn kho bằng transaction; nút Mua ngay chuyển thẳng tới thanh toán.
- Đặt hàng khóa dòng sản phẩm, không bán vượt tồn kho, không trừ kho hai lần, kiểm tra mã giảm giá đồng thời và miễn phí giao hàng từ 500.000đ.
- Khách hàng được sửa hồ sơ, đổi mật khẩu, xem đơn và hủy đơn đang chờ xác nhận. Khi hủy, tồn kho và lượt dùng mã giảm giá được hoàn lại.
- Chỉ khách đã có đơn hoàn thành mới được đánh giá; mỗi khách đánh giá một sản phẩm một lần.
- Admin có CRUD sản phẩm/danh mục/thương hiệu/mã giảm giá, tải ảnh sản phẩm, quản lý đơn theo đúng luồng trạng thái, quản lý đánh giá và khóa khách hàng.
- Ảnh URL, ảnh trong `public/uploads/products`, ảnh trong `public/storage` và ảnh thiếu đều có cơ chế hiển thị/fallback thống nhất.

## Cấu hình cơ sở dữ liệu

Các giá trị mặc định trong `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_phone_shop
DB_USERNAME=root
DB_PASSWORD=
```

Tên bảng và cột MySQL vẫn giữ theo file SQL gốc vì đó là định danh kỹ thuật được toàn bộ truy vấn sử dụng.

## Tìm code nhanh

- Controller người dùng: `app/Http/Controllers/MBA_TrangChuController.php`, `MBA_CuaHangController.php`, `MBA_ChiTietSanPhamController.php`, `MBA_GioHangController.php`, `MBA_ThanhToanController.php`, `MBA_TaiKhoanController.php`, `MBA_DanhGiaSanPhamController.php`.
- Controller quản trị: các file `TTB_QuanTriController.php` và `TTB_QuanLy...Controller.php`.
- Model dữ liệu: `app/Models/` với các lớp `SanPham`, `DonHang`, `KhachHang`, `GioHang`, `DanhMuc`, `ThuongHieu`, `DanhGia`, `MaGiamGia`.
- Giao diện người dùng: `resources/views/nguoi_dung/`.
- Giao diện quản trị: `resources/views/quan_tri/`.
- Route: `routes/web.php`.
- Sơ đồ chi tiết: `SO_DO_FILE_DU_AN.md`.
- Cẩm nang thuyết trình và trả lời câu hỏi: `CAM_NANG_BAO_CAO_DO_AN.md`.

## Những tên bắt buộc giữ nguyên

Các tên `app`, `bootstrap`, `config`, `public`, `resources`, `routes`, `storage`, `vendor`, `artisan`, `composer.json`, `.env` và các tên bảng/cột là cấu trúc hoặc định danh kỹ thuật của Laravel, Composer và MySQL. Không đổi các tên này nếu không muốn phá cơ chế nạp ứng dụng.

## Sửa lỗi thường gặp

Nếu vừa đổi file mà Laravel báo không tìm thấy lớp hoặc vẫn dùng giao diện cũ:

```powershell
composer dump-autoload
php artisan optimize:clear
```

Nếu lỗi HTTP 500, đọc dòng lỗi mới nhất:

```powershell
((Select-String -Path ".\storage\logs\laravel.log" -Pattern "local.ERROR" | Select-Object -Last 1).Line -split ' \{"view"')[0]
```

Thông báo `Download the React DevTools`, `Chrome Built-In AI` và lỗi `favicon.ico 404` không phải nguyên nhân làm Laravel trả về HTTP 500.

Nếu sản phẩm dùng tên ảnh mẫu nhưng chưa có tệp ảnh thật, website sẽ hiển thị ảnh mặc định thay vì biểu tượng ảnh hỏng. Admin có thể vào Sản phẩm → Sửa để tải tệp JPG/JPEG/PNG/WebP (tối đa 4 MB) hoặc dán URL ảnh.
