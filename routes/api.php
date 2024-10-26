<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(\App\Http\Controllers\Api\ProductController::class)
    ->prefix('products')
    ->group(function (){
        Route::get('/', 'index')->name('products.index');
        Route::get('/{product}', 'show')->name('products.show');
        Route::post('/', 'store')->name('products.store');
        Route::post('/{product}/reviews', 'review_store')->name('products.review_store');
    });
