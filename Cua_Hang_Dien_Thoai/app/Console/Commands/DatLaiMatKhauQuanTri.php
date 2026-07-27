<?php

namespace App\Console\Commands;

use App\Models\QuanTriVien;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DatLaiMatKhauQuanTri extends Command
{
    /** Tên và tham số của lệnh. */
    protected $signature = "quan-tri:dat-lai-mat-khau {ten_dang_nhap} {mat_khau}";

    /** Mô tả lệnh. */
    protected $description = "Đặt lại mật khẩu quản trị. Ví dụ: php artisan quan-tri:dat-lai-mat-khau admin 123456";

    /** Thực thi lệnh. */
    public function handle()
    {
        $username = $this->argument("ten_dang_nhap");
        $password = $this->argument("mat_khau");

        $admin = QuanTriVien::where("username", $username)->first();

        if (!$admin) {
            $this->error(
                "Không tồn tại quản trị viên có tên đăng nhập '$username'!",
            );
            return 1;
        }

        if (mb_strlen($password) < 8) {
            $this->error("Mật khẩu phải có ít nhất 8 ký tự.");
            return 1;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info("✓ Đặt lại mật khẩu thành công!");
        $this->info("Tên đăng nhập: $username");

        return 0;
    }
}
