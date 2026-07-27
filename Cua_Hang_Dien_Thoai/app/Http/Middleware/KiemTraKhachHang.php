<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KiemTraKhachHang
{
    public function handle(Request $request, Closure $next): mixed
    {
        $guard = Auth::guard("customer");
        if (!$guard->check()) {
            return redirect()
                ->route("login")
                ->with("error", "Vui long dang nhap de tiep tuc");
        }

        if ((int) $guard->user()->status !== 1) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route("login")
                ->with("error", "Tai khoan da bi khoa.");
        }
        return $next($request);
    }
}
