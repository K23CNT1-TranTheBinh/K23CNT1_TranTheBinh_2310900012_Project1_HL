# Sơ đồ file toàn dự án

Tên file PHP dùng tiếng Việt không dấu để tương thích Composer PSR-4 và Windows.
Tiền tố phân công được giữ nguyên: `MBA_` là phần người dùng của Mai Bình An,
`TTB_` là phần quản trị của Trần Thế Bình và `NVL_` là cơ sở dữ liệu của
Nguyễn Văn Lượng.

## Khu vực người dùng

| Chức năng | Controller | Giao diện |
| --- | --- | --- |
| Trang chủ | `MBA_TrangChuController.php` | `nguoi_dung/MBA_trang_chu.blade.php` |
| Cửa hàng, lọc và tìm kiếm | `MBA_CuaHangController.php` | `nguoi_dung/MBA_cua_hang.blade.php` |
| Chi tiết sản phẩm | `MBA_ChiTietSanPhamController.php` | `nguoi_dung/MBA_chi_tiet_san_pham.blade.php` |
| Giỏ hàng | `MBA_GioHangController.php` | `nguoi_dung/MBA_gio_hang.blade.php` |
| Thanh toán | `MBA_ThanhToanController.php` | `nguoi_dung/MBA_thanh_toan.blade.php` |
| Đặt hàng thành công | `MBA_ThanhToanController.php` | `nguoi_dung/MBA_dat_hang_thanh_cong.blade.php` |
| Tài khoản và đơn đã mua | `MBA_TaiKhoanController.php` | `nguoi_dung/MBA_tai_khoan.blade.php`, `MBA_don_hang_cua_toi.blade.php` |
| Đánh giá sản phẩm | `MBA_DanhGiaSanPhamController.php` | nằm trong trang chi tiết sản phẩm |
| Đăng nhập, đăng ký | `XacThucController.php` | `nguoi_dung/MBA_dang_nhap.blade.php`, `MBA_dang_ky.blade.php` |

Các Controller nằm trong `app/Http/Controllers/`. Bố cục chung là `resources/views/nguoi_dung/MBA_bo_cuc_nguoi_dung.blade.php`.

## Khu vực quản trị

| Chức năng | Controller | Giao diện chính |
| --- | --- | --- |
| Tổng quan | `TTB_QuanTriController.php` | `quan_tri/TTB_tong_quan.blade.php` |
| Sản phẩm | `TTB_QuanLySanPhamController.php` | `TTB_danh_sach_san_pham.blade.php`, `TTB_bieu_mau_san_pham.blade.php` |
| Đơn hàng | `TTB_QuanLyDonHangController.php` | `TTB_danh_sach_don_hang.blade.php`, `TTB_chi_tiet_don_hang.blade.php` |
| Danh mục | `TTB_QuanLyDanhMucController.php` | `TTB_danh_sach_danh_muc.blade.php`, `TTB_bieu_mau_danh_muc.blade.php` |
| Thương hiệu | `TTB_QuanLyThuongHieuController.php` | `TTB_danh_sach_thuong_hieu.blade.php`, `TTB_bieu_mau_thuong_hieu.blade.php` |
| Mã giảm giá | `TTB_QuanLyMaGiamGiaController.php` | `TTB_danh_sach_ma_giam_gia.blade.php`, `TTB_bieu_mau_ma_giam_gia.blade.php` |
| Đánh giá | `TTB_QuanLyDanhGiaController.php` | `TTB_danh_sach_danh_gia.blade.php` |
| Khách hàng | `TTB_QuanLyKhachHangController.php` | `TTB_danh_sach_khach_hang.blade.php` |

Giao diện quản trị nằm trong `resources/views/quan_tri/`. Bố cục chung là `TTB_bo_cuc_quan_tri.blade.php`.

## Model dữ liệu

| Dữ liệu | File |
| --- | --- |
| Sản phẩm | `app/Models/SanPham.php` |
| Đơn hàng | `app/Models/DonHang.php` |
| Chi tiết đơn hàng | `app/Models/ChiTietDonHang.php` |
| Khách hàng | `app/Models/KhachHang.php` |
| Quản trị viên | `app/Models/QuanTriVien.php` |
| Giỏ hàng | `app/Models/GioHang.php` |
| Danh mục | `app/Models/DanhMuc.php` |
| Thương hiệu | `app/Models/ThuongHieu.php` |
| Đánh giá | `app/Models/DanhGia.php` |
| Mã giảm giá | `app/Models/MaGiamGia.php` |

## File kết nối luồng

| Nội dung | File |
| --- | --- |
| URL và Controller xử lý | `routes/web.php` |
| Cấu hình đăng nhập | `config/auth.php` |
| Middleware bảo vệ trang | `app/Http/Middleware/KiemTraKhachHang.php`, `KiemTraQuanTri.php` |
| Thông báo kiểm tra dữ liệu | `lang/vi/validation.php` |
| Cơ sở dữ liệu và dữ liệu mẫu | `database/NVL_CoSoDuLieu_CuaHangDienThoai.sql` |
| Nâng cấp database cũ, giữ nguyên dữ liệu | `database/NVL_CapNhat_ChucNang.sql` |

## Luồng sửa sản phẩm

1. Route tài nguyên `admin.products` được khai báo trong `routes/web.php`.
2. `TTB_QuanLySanPhamController::edit()` lấy sản phẩm và mở `TTB_bieu_mau_san_pham.blade.php`.
3. Nút **Lưu sản phẩm** gọi `TTB_QuanLySanPhamController::update()`.
4. Hàm `validateProduct()` kiểm tra dữ liệu.
5. Model `SanPham` cập nhật bảng kỹ thuật `products` trong MySQL.

Sau khi đổi tên file, luôn chạy:

```powershell
composer dump-autoload
php artisan optimize:clear
```
