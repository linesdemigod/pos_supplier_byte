<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashMovementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['cash_movement.menu', 'cash_movement.create', 'cash_movement.edit', 'cash_movement.delete', 'cash_movement.approve'];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_name' => 'cash_movement',
                ]
            );
        }
    }
}
