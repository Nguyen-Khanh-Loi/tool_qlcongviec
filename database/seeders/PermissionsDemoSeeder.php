<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'access_administrator']);
        Permission::create(['name' => 'access_manager']);
        Permission::create(['name' => 'access_leader']);
        Permission::create(['name' => 'access_user']);
        $role = Role::create(['name' => 'administrator'])
            ->givePermissionTo(['access_administrator', 'access_leader','access_department', 'access_user']);
            $role = Role::create(['name' => 'manager'])
            ->givePermissionTo(['access_manager', 'access_leader', 'access_user']);
        $role = Role::create(['name' => 'leader'])
            ->givePermissionTo(['access_leader','access_user']);
        $role = Role::create(['name' => 'user'])
            ->givePermissionTo(['access_user']);

        $admin = User::create([
            'name' => "admin",
            'email' => "admin@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $admin->assignRole('administrator');
        
        $leader = User::create([
            'name' => "leader",
            'email' => "leader@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $leader->assignRole('leader');

        $leader = User::create([
            'name' => "manager",
            'email' => "manager@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $leader->assignRole('manager');

        $user = User::create([
            'name' => "user",
            'email' => "user@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole('user');
    }
}
