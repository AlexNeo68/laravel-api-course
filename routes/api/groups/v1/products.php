<?php

use App\Http\Controllers\Api\Product\ProductResourceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductResourceController::class);

Route::controller(ProductResourceController::class)
    ->prefix('products')
    ->group(function (){
        Route::post('/{product}/reviews', 'review_store')->name('products.review_store');
    });
