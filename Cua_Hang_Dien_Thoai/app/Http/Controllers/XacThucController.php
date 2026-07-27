<?php

namespace App\Http\Controllers;

use App\Models\QuanTriVien;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// XacThucController chung - dang nhap / dang ky / dang xuat cho ca customer va admin
class XacThucController extends BoDieuKhien
{
    // Hien thi form dang nhap
    public function showLogin()
    {
        if (Auth::guard("admin")->check()) {
            return redirect()->route("admin.dashboard");
        }
        if (Auth::guard("customer")->check()) {
            return redirect()->route("home");
        }

        return view("nguoi_dung.MBA_dang_nhap");
    }

    // Xu ly dang nhap (thu customer truoc, sau do admin)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "string", "max:100"],
            "password" => ["required", "string", "max:255"],
        ]);

        $input = trim($credentials["email"]);
        $password = $credentials["password"];

        // 1. Thu dang nhap voi KhachHang (tim theo email)
        $customer = KhachHang::where("email", mb_strtolower($input))->first();
        if ($customer && $this->passwordMatches($password, $customer->password)) {
            if ((int) $customer->status !== 1) {
                return back()
                    ->with(
                        "error",
                        "Tai khoan da bi khoa. Vui long lien he quan tri vien.",
                    )
                    ->withInput();
            }
            $this->upgradeLegacyPassword($customer, $password);
            Auth::guard("admin")->logout();
            Auth::guard("customer")->login(
                $customer,
                $request->boolean("remember"),
            );
            $request->session()->regenerate();
            return redirect()
                ->intended(route("home"))
                ->with(
                    "success",
                    "Dang nhap thanh cong. Xin chao " .
                        $customer->full_name .
                        "!",
                );
        }

        // 2. Thu dang nhap voi QuanTriVien (tim theo email hoac username)
        $admin = QuanTriVien::where("email", $input)
            ->orWhere("username", $input)
            ->first();
        if ($admin && $this->passwordMatches($password, $admin->password)) {
            if ((int) $admin->status !== 1) {
                return back()
                    ->with("error", "Tai khoan quan tri da bi khoa.")
                    ->withInput();
            }
            $this->upgradeLegacyPassword($admin, $password);
            Auth::guard("customer")->logout();
            Auth::guard("admin")->login($admin, $request->boolean("remember"));
            $request->session()->regenerate();
            return redirect()
                ->route("admin.dashboard")
                ->with("success", "Dang nhap quan tri thanh cong.");
        }

        return back()
            ->with("error", "Email/ten dang nhap hoac mat khau khong dung.")
            ->withInput();
    }

    // Hien thi form dang ky
    public function showRegister()
    {
        if (Auth::guard("admin")->check()) {
            return redirect()->route("admin.dashboard");
        }
        if (Auth::guard("customer")->check()) {
            return redirect()->route("home");
        }

        return view("nguoi_dung.MBA_dang_ky");
    }

    // Xu ly dang ky khach hang moi
    public function register(Request $request)
    {
        $data = $request->validate([
            "full_name" => ["required", "string", "max:100"],
            "email" => [
                "required",
                "email",
                "max:100",
                "unique:users,email",
            ],
            "phone" => [
                "nullable",
                "string",
                "regex:/^(0|\\+84)[0-9]{9,10}$/",
            ],
            "password" => ["required", "string", "min:8", "confirmed"],
        ]);

        if (
            QuanTriVien::where(
                "email",
                mb_strtolower(trim($data["email"])),
            )->exists()
        ) {
            return back()
                ->withErrors([
                    "email" => "Email nay da duoc su dung trong he thong.",
                ])
                ->withInput();
        }

        // Tai khoan moi dung bcrypt; tai khoan MD5 cu se tu nang cap khi dang nhap.
        $customer = KhachHang::create([
            "full_name" => trim($data["full_name"]),
            "email" => mb_strtolower(trim($data["email"])),
            "phone" => $data["phone"] ?? null,
            "password" => Hash::make($data["password"]),
            "status" => 1,
        ]);

        Auth::guard("customer")->login($customer);
        $request->session()->regenerate();

        return redirect()
            ->route("home")
            ->with(
                "success",
                "Dang ky thanh cong. Chao mung ban den voi PhoneShop!",
            );
    }

    // Dang xuat (chi customer)
    public function logout(Request $request)
    {
        Auth::guard("customer")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home")->with("success", "Ban da dang xuat.");
    }

    private function passwordMatches(string $plain, string $stored): bool
    {
        if (preg_match("/^[a-f0-9]{32}$/i", $stored) === 1) {
            return hash_equals(mb_strtolower($stored), md5($plain));
        }

        try {
            return Hash::check($plain, $stored);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function upgradeLegacyPassword($account, string $plain): void
    {
        if (preg_match("/^[a-f0-9]{32}$/i", (string) $account->password) === 1) {
            $account->password = Hash::make($plain);
            $account->save();
        }
    }
}
