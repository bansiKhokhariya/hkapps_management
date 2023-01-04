<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::create(['name' => 'dashboard-list']);
        Permission::create(['name' => 'user-list']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-delete']);
        Permission::create(['name' => 'task-list']);
        Permission::create(['name' => 'task-create']);
        Permission::create(['name' => 'task-edit']);
        Permission::create(['name' => 'task-delete']);


        $role1 = Role::create(['name' => 'admin']);
        $role1->givePermissionTo('dashboard-list');
        $role1->givePermissionTo('user-list');
        $role1->givePermissionTo('user-create');
        $role1->givePermissionTo('user-edit');
        $role1->givePermissionTo('user-delete');
        $role1->givePermissionTo('task-list');
        $role1->givePermissionTo('task-create');
        $role1->givePermissionTo('task-edit');
        $role1->givePermissionTo('task-delete');



        $role2 = Role::create(['name' => 'user']);
        $role2->givePermissionTo('dashboard-list');
        $role2->givePermissionTo('task-list');


        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'designation'=> 'admins',
            'roles'=> 'admin'
        ]);

        $user->assignRole($role1);

    }
}
