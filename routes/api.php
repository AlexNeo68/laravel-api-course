<?php

use App\Http\Controllers\Api\Product\ProductResourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductResourceController::class);

Route::controller(ProductResourceController::class)
    ->prefix('products')
    ->group(function (){
        Route::post('/{product}/reviews', 'review_store')->name('products.review_store');
    });


Route::controller(\App\Http\Controllers\Api\User\UserController::class)
    ->group(function(){
    Route::post('login', 'login')->name('user.login');
});
