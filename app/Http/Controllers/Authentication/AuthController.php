<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        User::create(
            $request->only(
            'first_name',
            'last_name',
            'username',
            'email',
            'phone'
            ) + [
                'password' => Hash::make($request->input('password'))
            ]
        );

        return response(['message' => 'Registration successful'], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('user')->accessToken;

            return response(['token' => $token], Response::HTTP_ACCEPTED);
        }

        return response(["error" => "Username or Password does not match"]);        
    }
}
