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

        Permission::create(['name' => 'dashboard-view']);
        Permission::create(['name' => 'user-view']);
        Permission::create(['name' => 'user-store']);
        Permission::create(['name' => 'user-update']);
        Permission::create(['name' => 'user-delete']);
        Permission::create(['name' => 'task-view']);
        Permission::create(['name' => 'task-store']);
        Permission::create(['name' => 'task-update']);
        Permission::create(['name' => 'task-delete']);
        Permission::create(['name' => 'webcreon2-view']);
        Permission::create(['name' => 'webcreon2-update']);
        Permission::create(['name' => 'allApp-view']);
        Permission::create(['name' => 'allApp-store']);
        Permission::create(['name' => 'allApp-update']);
        Permission::create(['name' => 'allApp-delete']);
        Permission::create(['name' => 'apikey-view']);
        Permission::create(['name' => 'apikey-delete']);
        Permission::create(['name' => 'apikey-assign']);
        Permission::create(['name' => 'platform-view']);
        Permission::create(['name' => 'platform-store']);
        Permission::create(['name' => 'platform-update']);
        Permission::create(['name' => 'platform-delete']);
        Permission::create(['name' => 'advertise-view']);
        Permission::create(['name' => 'advertise-store']);
        Permission::create(['name' => 'advertise-update']);
        Permission::create(['name' => 'advertise-delete']);
        Permission::create(['name' => 'expenseRevenue-view']);
        Permission::create(['name' => 'expenseRevenue-store']);
        Permission::create(['name' => 'expenseRevenue-update']);
        Permission::create(['name' => 'expenseRevenue-delete']);
        Permission::create(['name' => 'expense-store']);
        Permission::create(['name' => 'revenue-store']);
        Permission::create(['name' => 'ads-view']);
        Permission::create(['name' => 'ads-store']);
        Permission::create(['name' => 'ads-update']);
        Permission::create(['name' => 'ads-delete']);
        Permission::create(['name' => 'adx-view']);
        Permission::create(['name' => 'adx-store']);
        Permission::create(['name' => 'adx-update']);
        Permission::create(['name' => 'adx-delete']);
        Permission::create(['name' => 'party-view']);
        Permission::create(['name' => 'party-store']);
        Permission::create(['name' => 'party-update']);
        Permission::create(['name' => 'party-delete']);
        Permission::create(['name' => 'activityLog-view']);
        Permission::create(['name' => 'permission-update']);
        Permission::create(['name' => 'companyMaster-view']);
        Permission::create(['name' => 'companyMaster-store']);
        Permission::create(['name' => 'companyMaster-update']);
        Permission::create(['name' => 'companyMaster-delete']);
        Permission::create(['name' => 'analytics-view']);
        Permission::create(['name' => 'backgroundTask-view']);
        Permission::create(['name' => 'systemSettings-view']);
        Permission::create(['name' => 'notification-view']);
        Permission::create(['name' => 'spyApp-view']);
        Permission::create(['name' => 'spyApp-store']);
        Permission::create(['name' => 'commanMaster-view']);
        Permission::create(['name' => 'commanMaster-store']);
        Permission::create(['name' => 'commanMaster-update']);
        Permission::create(['name' => 'commanMaster-delete']);
        Permission::create(['name' => 'adsNetwork-view']);
        Permission::create(['name' => 'adsNetwork-store']);
        Permission::create(['name' => 'adsNetwork-update']);
        Permission::create(['name' => 'adsNetwork-delete']);
        Permission::create(['name' => 'todo-view']);
        Permission::create(['name' => 'todo-store']);
        Permission::create(['name' => 'todo-update']);
        Permission::create(['name' => 'todo-delete']);


        $role1 = Role::create(['name' => 'super_admin']);
        $role1->givePermissionTo('dashboard-view');
        $role1->givePermissionTo('user-view');
        $role1->givePermissionTo('user-store');
        $role1->givePermissionTo('user-update');
        $role1->givePermissionTo('user-delete');
        $role1->givePermissionTo('task-view');
        $role1->givePermissionTo('task-store');
        $role1->givePermissionTo('task-update');
        $role1->givePermissionTo('task-delete');
        $role1->givePermissionTo('webcreon2-view');
        $role1->givePermissionTo('webcreon2-update');
        $role1->givePermissionTo('allApp-view');
        $role1->givePermissionTo('allApp-store');
        $role1->givePermissionTo('allApp-update');
        $role1->givePermissionTo('allApp-delete');
        $role1->givePermissionTo('apikey-view');
        $role1->givePermissionTo('apikey-delete');
        $role1->givePermissionTo('apikey-assign');
        $role1->givePermissionTo('platform-view');
        $role1->givePermissionTo('platform-store');
        $role1->givePermissionTo('platform-update');
        $role1->givePermissionTo('platform-delete');
        $role1->givePermissionTo('advertise-view');
        $role1->givePermissionTo('advertise-store');
        $role1->givePermissionTo('advertise-update');
        $role1->givePermissionTo('advertise-delete');
        $role1->givePermissionTo('expenseRevenue-view');
        $role1->givePermissionTo('expenseRevenue-store');
        $role1->givePermissionTo('expenseRevenue-update');
        $role1->givePermissionTo('expenseRevenue-delete');
        $role1->givePermissionTo('expense-store');
        $role1->givePermissionTo('revenue-store');
        $role1->givePermissionTo('ads-view');
        $role1->givePermissionTo('ads-store');
        $role1->givePermissionTo('ads-update');
        $role1->givePermissionTo('ads-delete');
        $role1->givePermissionTo('adx-view');
        $role1->givePermissionTo('adx-store');
        $role1->givePermissionTo('adx-update');
        $role1->givePermissionTo('adx-delete');
        $role1->givePermissionTo('party-view');
        $role1->givePermissionTo('party-store');
        $role1->givePermissionTo('party-update');
        $role1->givePermissionTo('party-delete');
        $role1->givePermissionTo('activityLog-view');
        $role1->givePermissionTo('permission-update');
        $role1->givePermissionTo('companyMaster-view');
        $role1->givePermissionTo('companyMaster-store');
        $role1->givePermissionTo('companyMaster-update');
        $role1->givePermissionTo('companyMaster-delete');
        $role1->givePermissionTo('analytics-view');
        $role1->givePermissionTo('backgroundTask-view');
        $role1->givePermissionTo('systemSettings-view');
        $role1->givePermissionTo('notification-view');
        $role1->givePermissionTo('spyApp-view');
        $role1->givePermissionTo('spyApp-store');
        $role1->givePermissionTo('commanMaster-view');
        $role1->givePermissionTo('commanMaster-store');
        $role1->givePermissionTo('commanMaster-update');
        $role1->givePermissionTo('commanMaster-delete');
        $role1->givePermissionTo('adsNetwork-view');
        $role1->givePermissionTo('adsNetwork-store');
        $role1->givePermissionTo('adsNetwork-update');
        $role1->givePermissionTo('adsNetwork-delete');
        $role1->givePermissionTo('todo-view');
        $role1->givePermissionTo('todo-store');
        $role1->givePermissionTo('todo-update');
        $role1->givePermissionTo('todo-delete');


        $user = User::create([
            'name' => 'super admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
            'designation'=> 'superadmin',
            'roles'=> 'super_admin'
        ]);

        $user->assignRole($role1);

    }
}
