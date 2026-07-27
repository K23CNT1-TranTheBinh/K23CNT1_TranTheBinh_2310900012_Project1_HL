<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KiemTraQuanTri
{
    public function handle(Request $request, Closure $next): mixed
    {
        $guard = Auth::guard("admin");
        if (!$guard->check()) {
            return redirect()
                ->route("login")
                ->with(
                    "error",
                    "Vui long dang nhap bang tai khoan admin de vao quan tri.",
                );
        }

        if ((int) $guard->user()->status !== 1) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route("login")
                ->with("error", "Tai khoan quan tri da bi khoa.");
        }
        return $next($request);
    }
}
