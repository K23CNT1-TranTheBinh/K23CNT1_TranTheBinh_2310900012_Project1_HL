<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

// Model KhachHang - khach hang (giu ten table 'users' theo SQL goc)
class KhachHang extends Authenticatable
{
    protected $table = "users";

    public $timestamps = true;

    protected $fillable = [
        "email",
        "password",
        "full_name",
        "phone",
        "address",
        "avatar",
        "status",
    ];

    protected $hidden = ["password"];

    // Tra ve password md5 de Auth so sanh
    public function getAuthPassword()
    {
        return $this->password;
    }

    // Gio hang cua khach hang
    public function carts()
    {
        return $this->hasMany(GioHang::class, "user_id");
    }

    // Don hang cua khach hang
    public function orders()
    {
        return $this->hasMany(DonHang::class, "user_id");
    }

    // Danh gia cua khach hang
    public function reviews()
    {
        return $this->hasMany(DanhGia::class, "user_id");
    }
}
