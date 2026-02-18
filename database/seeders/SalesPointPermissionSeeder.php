<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SalesPointPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales_point_permissions')->insert([
            'id' => 1,
            'user_id' => 1,
            'permission_name' => 'allow_negative',

        ]);
        DB::table('sales_point_permissions')->insert([
            'id' => 2,
            'user_id' => 1,
            'permission_name' => 'price_edit',

        ]);
    }
}
