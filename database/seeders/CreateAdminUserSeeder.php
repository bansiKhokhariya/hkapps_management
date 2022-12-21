<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard-list',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'task-list',
            'task-create',
            'task-edit',
            'task-delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::find(1);
        $role->givePermissionTo($permission);


        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }

}
