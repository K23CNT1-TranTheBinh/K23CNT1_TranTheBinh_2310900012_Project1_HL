<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model ThuongHieu - thuong hien dien thoai
class ThuongHieu extends Model
{
    protected $table = "brands";

    public $timestamps = false;

    protected $fillable = ["name", "slug", "logo", "status"];

    // Mot thuong hieu co nhieu san pham
    public function products()
    {
        return $this->hasMany(SanPham::class, "brand_id");
    }
}
