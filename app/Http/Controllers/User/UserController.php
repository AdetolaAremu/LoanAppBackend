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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'users');
        
        return UserResource::collection(User::get(), Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions()
            ]
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        Gate::authorize('view', 'users');

        $user = User::find($id);

        if (!$user) {
            return response(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->only('role_id'));

        return response(['message' => 'Role updated successfully']);
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
        Gate::authorize('view', 'users');
        
        User::destroy($id);

        return response(['message' => 'User deleted successfully']);
    }

    public function currentUser()
    {
        $user = Auth::user();

        // return response($user, Response::HTTP_ACCEPTED);

        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions()
            ]
        ]);
    }
}
