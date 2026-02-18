<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'permissions', 'guard_name' => 'web', 'group_name' => 'permission'],
            ['name' => 'report', 'guard_name' => 'web', 'group_name' => 'report'],
            ['name' => 'audit', 'guard_name' => 'web', 'group_name' => 'audit'],
            ['name' => 'company', 'guard_name' => 'web', 'group_name' => 'company'],
            ['name' => 'saleSession.menu', 'guard_name' => 'web', 'group_name' => 'saleSession'],
            ['name' => 'saleSession.day', 'guard_name' => 'web', 'group_name' => 'saleSession'],
            ['name' => 'saleSession.month', 'guard_name' => 'web', 'group_name' => 'saleSession'],
            ['name' => 'shop.menu', 'guard_name' => 'web', 'group_name' => 'shop'],
            ['name' => 'customer.menu', 'guard_name' => 'web', 'group_name' => 'customer'],
            ['name' => 'customer.create', 'guard_name' => 'web', 'group_name' => 'customer'],
            ['name' => 'customer.edit', 'guard_name' => 'web', 'group_name' => 'customer'],
            ['name' => 'customer.delete', 'guard_name' => 'web', 'group_name' => 'customer'],
            ['name' => 'customer.import', 'guard_name' => 'web', 'group_name' => 'customer'],
            ['name' => 'returnItem.menu', 'guard_name' => 'web', 'group_name' => 'returnItem'],
            ['name' => 'returnItem.create', 'guard_name' => 'web', 'group_name' => 'returnItem'],
            ['name' => 'returnItem.delete', 'guard_name' => 'web', 'group_name' => 'returnItem'],
            ['name' => 'sale.menu', 'guard_name' => 'web', 'group_name' => 'sale'],
            ['name' => 'sale.show', 'guard_name' => 'web', 'group_name' => 'sale'],
            ['name' => 'sale.print', 'guard_name' => 'web', 'group_name' => 'sale'],
            ['name' => 'inventory.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'category.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'category.create', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'category.edit', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'category.delete', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'category.import', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'item.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'item.create', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'item.edit', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'item.delete', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'item.import', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'storeInventory.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'storeInventory.create', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'storeInventory.update', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'storeInventory.import', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'warehouseInventory.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'warehouseInventory.create', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'warehouseInventory.update', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'warehouseInventory.import', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'priceManagement.menu', 'guard_name' => 'web', 'group_name' => 'inventory'],
            ['name' => 'utilities.menu', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'storeRequest.menu', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'storeRequest.create', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'storeRequest.edit', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'storeRequest.delete', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'transferOrder.menu', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'transferOrder.edit', 'guard_name' => 'web', 'group_name' => 'utilities'],
            ['name' => 'administrativeUtility.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'systemUser.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'systemUser.create', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'systemUser.edit', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'systemUser.delete', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'systemUser.import', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'branch.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'switchBranch.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'timeRestriction.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'store.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'store.create', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'store.edit', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'store.delete', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'warehouse.menu', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'warehouse.create', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'warehouse.edit', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
            ['name' => 'warehouse.delete', 'guard_name' => 'web', 'group_name' => 'adminstrativeUtility'],
        ];

        foreach ($values as $value) {
            DB::table('permissions')->insert([
                'name' => $value['name'],
                'guard_name' => $value['guard_name'],
                'group_name' => $value['group_name'],
            ]);
        }
    }
}
