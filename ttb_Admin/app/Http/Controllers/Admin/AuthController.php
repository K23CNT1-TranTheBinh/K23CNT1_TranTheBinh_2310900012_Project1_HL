<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function postLogin(Request $request)
    {
        $admin = Admin::where('username',$request->username)
                        ->where('password',md5($request->password))
                        ->where('status',1)
                        ->first();

        if($admin)
        {
            session([
                'admin_id'=>$admin->id,
                'admin_name'=>$admin->full_name,
                'admin_role'=>$admin->role
            ]);

            return redirect('/admin/dashboard');
        }

        return back()->with(
            'error',
            'Sai tài khoản hoặc mật khẩu'
        );
    }

    public function logout()
    {
        session()->flush();

        return redirect('/admin/login');
    }
}