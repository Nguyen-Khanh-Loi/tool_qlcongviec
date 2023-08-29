<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AddPermisssionAndRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'access_supper_administrator']);
        Permission::create(['name' => 'access_project_manager']);
        $role = Role::create(['name' => 'supper_administrator'])
        ->givePermissionTo(['access_supper_administrator', 'access_administrator', 'access_manager', 'access_leader', 'access_user']);
        $role = Role::create(['name' => 'project_manager'])
        ->givePermissionTo(['access_project_management', 'access_leader', 'access_user']);
        $supper_admin = User::create([
            'name' => "supper_admin",
            'email' => "supperadmin@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $supper_admin->assignRole('supper_administrator');
        $project_management = User::create([
            'name' => "project_manager",
            'email' => "projectmanager@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $project_management->assignRole('project_manager');
        $project_management = User::create([
            'name' => "project_manager",
            'email' => "project_manager1@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $project_management->assignRole('project_manager');
    }
}
