<?php
return [
    "defaults" => ["guard" => "customer", "passwords" => "customers"],
    "guards" => [
        "customer" => ["driver" => "session", "provider" => "customers"],
        "admin" => ["driver" => "session", "provider" => "admins"],
    ],
    "providers" => [
        "customers" => [
            "driver" => "eloquent",
            "model" => App\Models\KhachHang::class,
        ],
        "admins" => [
            "driver" => "eloquent",
            "model" => App\Models\QuanTriVien::class,
        ],
    ],
    "passwords" => [
        "customers" => [
            "provider" => "customers",
            "table" => "password_reset_tokens",
            "expire" => 60,
            "throttle" => 60,
        ],
        "admins" => [
            "provider" => "admins",
            "table" => "password_reset_tokens",
            "expire" => 60,
            "throttle" => 60,
        ],
    ],
    "password_timeout" => 10800,
];
