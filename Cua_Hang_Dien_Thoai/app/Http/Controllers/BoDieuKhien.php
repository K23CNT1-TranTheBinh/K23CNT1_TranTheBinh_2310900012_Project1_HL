<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class BoDieuKhien extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function vnd($amount): string
    {
        return number_format((float) ($amount ?? 0), 0, ",", ".") . " ₫";
    }

    protected function slugify(string $text): string
    {
        return Str::slug($text);
    }
}
