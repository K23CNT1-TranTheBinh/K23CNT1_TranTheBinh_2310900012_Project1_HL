<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

// Model QuanTriVien - tai khoan quan tri (dang nhap guard admin, password md5)
class QuanTriVien extends Authenticatable
{
    protected $table = "admins";

    public $timestamps = false;

    protected $fillable = [
        "username",
        "password",
        "full_name",
        "email",
        "phone",
        "role",
        "status",
    ];

    protected $hidden = ["password"];

    // Tra ve password md5 de Auth so sanh
    public function getAuthPassword()
    {
        return $this->password;
    }
}
