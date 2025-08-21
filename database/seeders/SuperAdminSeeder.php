<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $data = new User();
    //     $data->name = 'Super Admin';
    //     $data->email = 'superadmin@demo.com';
    //     $data->password = Hash::make('12345678');
    //     $data->role_id = 1; // Assuming the role_id for Super Admin is 1
    //     $data->save();
    // }



    public function run(): void
{
    $users = [
        [
            'name' => 'Super Admin',
            'email' => 'superadmin@demo.com',
            'password' => '12345678',
            'role_id' => 1,
        ],
        [
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => '12345678',
            'role_id' => 2,
        ],
        [
            'name' => 'Manager',
            'email' => 'manager@demo.com',
            'password' => '12345678',
            'role_id' => 3,
        ],
    ];

    foreach ($users as $user) {
        User::updateOrCreate(
            ['email' => $user['email']],
            [
                'name' => $user['name'],
                'password' => Hash::make($user['password']),
                'role_id' => $user['role_id'],
            ]
        );
    }
}

}
