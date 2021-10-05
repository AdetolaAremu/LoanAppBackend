<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\KYCRequest;
use App\Http\Requests\UpdateKYCRequest;
use App\Http\Resources\UserResource;
use App\Models\KnowCustomer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::get());

        // return UserResource::collection($user, Response::HTTP_ACCEPTED);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        return new UserResource($user);
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'email' => 'email'
        ]);

        $user = Auth::user();

        $user->update($request->only('first_name','last_name','email'));

        return response(['message' => 'Info Update successfully']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response(['message' => 'Password update successfully']);
    }

    public function destroy($id)
    {
        User::destroy($id);

        return response(['message' => 'User deleted successfully']);
    }

    public function currentUser()
    {
        $user = Auth::user();

        return response($user, Response::HTTP_ACCEPTED);
    }
}
