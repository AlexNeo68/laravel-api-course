<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function login(LoginRequest $request){

        if(!Auth::guard('web')->attempt(['email'=>$request->input('email'), 'password' => $request->input('password')])){
            return response()->json([
                'error' => 'Неправильные логин или пароль'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::guard('web')->user();
        $token = $user->createToken('login');

        return response()->json([
            'token' => $token->plainTextToken
        ], Response::HTTP_ACCEPTED);
    }
}
