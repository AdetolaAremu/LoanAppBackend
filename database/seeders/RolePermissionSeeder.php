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
        $admin = Role::whereName('admin')->first();

        foreach ($permissions as $permission) {
                DB::table('role_permissions')->insert([
                'role_id' => $admin->id,
                'permission_id' => $permission->id
            ]);
        }

        $user = Role::whereName('user')->first();
        foreach ($permissions as $permission) {
            if (!in_array($permission->name, ['view_users'])) {
                    DB::table('role_permissions')->insert([
                    'role_id' => $admin->id,
                    'permission_id' => $permission->id
                ]);
            }
        }        
    }
}
