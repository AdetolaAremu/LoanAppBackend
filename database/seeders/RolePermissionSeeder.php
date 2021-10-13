<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();
        $admin = Role::whereName('Admin')->first();

        foreach ($permissions as $permission) {
                DB::table('role_permissions')->insert([
                'role_id' => $admin->id,
                'permission_id' => $permission->id
            ]);
        }

        $user = Role::whereName('User')->first();

        foreach ($permissions as $permission) {
            if (!in_array($permission->name, ['create_roles'])) {
                    DB::table('role_permissions')->insert([
                    'role_id' => $admin->id,
                    'permission_id' => $permission->id
                ]);
            }
        }        
    }
}
