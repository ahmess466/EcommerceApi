<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});
//Brand Crud Operaation
Route::group(['prefix'=>'brands'],function($router){
Route::controller(BrandController::class)->group(function(){
    Route::get('/index', 'index')->middleware('is_admin');
    Route::get('/show/{id}', 'show')->middleware('is_admin');
    Route::post('/store', 'store')->middleware('is_admin');
    Route::put('/update/{id}', 'update')->middleware('is_admin');
    Route::delete('/delete/{id}', 'destroy')->middleware('is_admin');
});
});
//Category Crud
Route::group(['prefix'=>'category'],function($router){
Route::controller(CategoryController::class)->group(function(){
    Route::get('/index', 'index')->middleware('is_admin');
    Route::get('/show/{id}', 'show')->middleware('is_admin');
    Route::post('/store', 'store')->middleware('is_admin');
    Route::put('/update_category/{id}', 'update')->middleware('is_admin');
    Route::delete('/delete_category/{id}', 'destroy')->middleware('is_admin');
    });
});
Route::group(['prefix'=>'location'],function($router){
Route::controller(LocationController::class)->group(function(){
    Route::post('/store','store')->middleware('auth:api');
    Route::put('/update/{$id}','update')->middleware('auth:api');
    Route::delete('/delete/{id}','delete')->middleware('auth:api');

});
});
Route::group(['prefix'=>'product'],function($router){
Route::controller(ProductsController::class)->group(function(){
    Route::get('/index', 'index')->middleware('auth:api');
    Route::get('/show/{id}', 'show')->middleware('auth:api');
    Route::post('/store', 'store')->middleware('is_admin');
    Route::put('/update/{id}', 'update')->middleware('is_admin');
    Route::delete('/delete/{id}', 'destroy')->middleware('is_admin');
    });
    });
    //Order Crud
    Route::group(['prefix'=>'order'],function($router){
    Route::controller(OrderController::class)->group(function(){
        Route::get('/index', 'index')->middleware('is_admin');
        Route::get('/show/{id}','show')->middleware('is_admin');
        Route::post('/store','store')->middleware('auth:api');
        Route::get('/get_order_items/{id}','get_order_items')->middleware('is_admin');
        Route::get('/get_user_orders/{id}','get_user_orders')->middleware('is_admin');
        Route::post('/change_order_status/{id}','change_order_status')->middleware('is_admin');



    });
    });




