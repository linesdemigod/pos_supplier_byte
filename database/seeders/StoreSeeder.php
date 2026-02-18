<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stores')->insert([
            'id' => 1,
            'company_id' => 1,
            'name' => 'Store 1',
            'location' => 'Address 1',
            'phone' => '1234567890',
        ]);
    }
}
