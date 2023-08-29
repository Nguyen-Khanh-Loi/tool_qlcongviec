<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class addUserSeederLeader1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $user = User::create([
            'name' => "leadertester",
            'email' => "leadertester@gmail.com",
            'first_name' => "",
            'last_name' => "",
            'phone' => "",
            'gender' => "",
            'address' => "",
            'status' => 1,
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole('leader');
    }
}
