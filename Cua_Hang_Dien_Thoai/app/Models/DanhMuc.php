<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model DanhMuc - danh muc san pham
class DanhMuc extends Model
{
    protected $table = "categories";

    public $timestamps = false;

    protected $fillable = ["name", "slug", "description", "status"];

    // Mot danh muc co nhieu san pham
    public function products()
    {
        return $this->hasMany(SanPham::class, "category_id");
    }
}
