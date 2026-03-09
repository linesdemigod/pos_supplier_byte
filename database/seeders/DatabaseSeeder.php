<?php

namespace Database\Seeders;

use App\Models\BranchSwitch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\TimeRestriction;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\NewRolePermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Company::factory(1)->create();
        // Store::factory(1)->create();
        // User::factory(1)->create();
        // BranchSwitch::factory(1)->create();
        // TimeRestriction::factory(1)->create();

        Category::factory(10)->create();
        Item::factory(20)->create();
        StoreInventory::factory(20)->create();
        Customer::factory(5)->create();
        Warehouse::factory(1)->create();

        // $this->call([

        //     CompanySeeder::class,
        //     StoreSeeder::class,
        //     RoleSeeder::class,
        //     RolePermissionSeeder::class,
        //     PermissionSeeder::class,
        //     UserSeeder::class,
        //     BranchSwitchSeeder::class,
        //     TimeRestrictionSeeder::class,
        //     SalesPointPermissionSeeder::class
        //     NewRolePermissionSeeder::class
        // ]);


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
