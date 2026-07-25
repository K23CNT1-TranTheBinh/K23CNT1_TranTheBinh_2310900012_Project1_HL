<?php

use Illuminate\Support\Facades\Route;
use App\Models\Admin;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/login',[AuthController::class,'login']);

Route::post('/admin/login',[AuthController::class,'postLogin']);


Route::middleware('admin')->group(function(){

    Route::get('/admin/dashboard',
        [DashboardController::class,'index']
    );


    Route::get('/admin/logout',
        [AuthController::class,'logout']
    );


    Route::resource(
        '/admin/categories',
        CategoryController::class
    );


    Route::get(
        '/admin/categories/status/{id}',
        [CategoryController::class,'changeStatus']
    );

});