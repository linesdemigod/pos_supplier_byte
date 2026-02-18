<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TimeRestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('time_restrictions')->insert([
            'user_id' => 1,
            'start_time' => '06:00:00',
            'end_time' => '18:00:00',
            'user_exemptions' => "[1]"
        ]);
    }
}
