<?php

namespace Database\Seeders;

use App\Models\RoleAndPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = new RoleAndPermission();
        $data->role_name = 'Super Admin';
        $data->sidebar_permissions = json_encode(
            [
                'dashboard',
            ]
        );
        // $data->page_wise_permissions = json_encode(
        //     [
        //         'dashboard' => [
        //             'view',
        //             'edit',
        //         ],
        //     ]
        // );
        $data->save();
    }
}
