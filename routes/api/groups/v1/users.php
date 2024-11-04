<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Api\User\UserController::class)
    ->group(function(){
        Route::post('login', 'login')->name('user.login');
    });
