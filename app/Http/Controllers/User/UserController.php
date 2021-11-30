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
    // get all users
    public function index()
    {
        // only a user with view users permissions can view this resource
        Gate::authorize('view', 'users');

        $user = User::get();

        return response($user, Response::HTTP_OK);
    }

    // get a user with additional resource such as role and permissions
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found']);
        }

        return (new UserResource($user))->additional([
            'data' => [
                'role' => $user->role,
                'permissions' => $user->permissions()
            ]
        ]);
    }

    // update role
    public function updateRole(Request $request, $id)
    {
        Gate::authorize('view', 'users');

        $user = User::find($id);

        if (!$user) {
            return response(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->only('role_id'));

        return response(['message' => 'Role updated successfully'], Response::HTTP_OK);
    }

    // update information of a looged in user
    public function updateInfo(Request $request)
    {
        $request->validate([
            'email' => 'email'
        ]);

        $user = Auth::user();

        $user->update($request->only('first_name','last_name','email', 'phone number'));

        return response(['message' => 'Info Update successfully'], Response::HTTP_OK);
    }

    // update the password of a logged in user
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

        return response(['message' => 'Password update successfully'], Response::HTTP_OK);
    }

    // delete user
    public function destroy($id)
    {
        Gate::authorize('view', 'users');
        
        User::destroy($id);

        return response(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }

    // get logged in user with roles, the frontend doesn't need the permissions collection
    public function currentUser()
    {
        $user = Auth::user();

        $kyc = KnowCustomer::where('user_id', $user->id)->first();

        return (new UserResource($user))->additional([
            'data' => [
                'role' => $user->role,
                'permissions' => $user->permissions()
            ]
        ], Response::HTTP_OK);
    }
}
