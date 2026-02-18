<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'id' => 1,
            'role' => 'admin',
            'name' => 'admin',
            'store_id' => 1,
            'username' => 'admin',
            'username_verified_at' => now(),
            'password' => Hash::make('123456'),
            'remember_token' => 'kPwmBM5unN',
            'created_at' => now(),
        ]);

        $user->assignRole('admin');
    }
}
