<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;



class UserController extends Controller
{


// Danh sách khách hàng
public function index(Request $request)
{

    $keyword = $request->keyword;


    $users = User::when($keyword,function($query) use($keyword){


        return $query

        ->where('full_name','like','%'.$keyword.'%')

        ->orWhere('email','like','%'.$keyword.'%')

        ->orWhere('phone','like','%'.$keyword.'%');


    })

    ->orderBy('id','asc')

    ->paginate(10);



    return view(
        'admin.users.index',
        compact(
            'users',
            'keyword'
        )
    );


}





// Xem chi tiết khách hàng
public function show($id)
{

    $user = User::findOrFail($id);


    return view(
        'admin.users.show',
        compact('user')
    );

}




// Xóa khách hàng
public function destroy($id)
{

    User::findOrFail($id)->delete();


    return back()->with(
        'success',
        'Xóa khách hàng thành công'
    );

}



}