<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['credit.menu', 'credit.summary', 'credit.detail', 'credit.void'];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_name' => 'credit',
                ]
            );
        }
    }
}
