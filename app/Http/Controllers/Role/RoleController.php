<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'users');
        
        return Role::get();
    }

    public function show($id)
    {
        Gate::authorize('view', 'users');

        $role = Role::find($id);

        if (!$role) {
            return response(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        return response($role, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        Gate::authorize('view', 'users');

        $role = Role::create($request->only('name'));

        if ($permissions = $request->input('permissions')) {
            foreach ($permissions as $permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        }

        return response(['message' => 'Role created successfully'], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('view', 'users');

        $role = Role::find($id);

        if (!$role) {
            return response(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        DB::table('role_permission')->where('role_id', $role->id)->delete();

        if ($permissions = $request->input('permissions')) {
            foreach ($permissions as $permission) {
                DB::table('role_permission')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        }

        $role->update($request->all());

        return response(['message' => 'Role successfully updated']);
    }

    public function destroy($id)
    {
        Gate::authorize('view', 'users');

        $role = Role::find($id);

        if (!$role) {
            return response(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $role->delete();

        DB::table('role_permission')->where('role_id', $id)->delete();

        return response(['message' => 'Role deleted successfully']);
    }
}
