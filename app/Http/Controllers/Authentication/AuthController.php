<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register user
    public function register(RegisterRequest $request)
    {     
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = 2;
        $user->password = Hash::make($request->password);
        $user->save();

        return response(['message' => 'Registration successful'], Response::HTTP_CREATED);
    }

    // login user with passport, jwt and cookie
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('user')->accessToken;

            $cookie = cookie('jwt', $token, 7200);

            return response(['token' => $token], Response::HTTP_OK)->withCookie($cookie);
        }

        return response(["error" => "Username or Password does not match"], Response::HTTP_BAD_REQUEST);     
    }

    // logout user
    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        return response(['message' => 'Logout successful'], Response::HTTP_OK)->withCookie($cookie);
    }
}
