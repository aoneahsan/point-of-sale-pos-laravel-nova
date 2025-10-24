<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $mainStore = Store::where('code', 'MAIN-001')->first();
        $northStore = Store::where('code', 'NORTH-001')->first();
        $southStore = Store::where('code', 'SOUTH-001')->first();

        // Super Admin (not tied to any store)
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => null,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('Super Admin');

        // Main Store Manager
        $mainManager = User::create([
            'name' => 'John Manager',
            'email' => 'manager@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $mainStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $mainManager->assignRole('Store Manager');

        // Main Store Cashiers
        $cashier1 = User::create([
            'name' => 'Sarah Cashier',
            'email' => 'cashier1@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $mainStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $cashier1->assignRole('Cashier');

        $cashier2 = User::create([
            'name' => 'Mike Cashier',
            'email' => 'cashier2@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $mainStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $cashier2->assignRole('Cashier');

        // Main Store Inventory Manager
        $inventoryManager = User::create([
            'name' => 'Emma Inventory',
            'email' => 'inventory@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $mainStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $inventoryManager->assignRole('Inventory Manager');

        // Main Store Accountant
        $accountant = User::create([
            'name' => 'David Accountant',
            'email' => 'accountant@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $mainStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('Accountant');

        // North Branch Users
        $northManager = User::create([
            'name' => 'Lisa Manager',
            'email' => 'north.manager@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $northStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $northManager->assignRole('Store Manager');

        $northCashier = User::create([
            'name' => 'Tom Cashier',
            'email' => 'north.cashier@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $northStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $northCashier->assignRole('Cashier');

        // South Branch Users
        $southManager = User::create([
            'name' => 'Rachel Manager',
            'email' => 'south.manager@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $southStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $southManager->assignRole('Store Manager');

        $southCashier = User::create([
            'name' => 'Chris Cashier',
            'email' => 'south.cashier@posstore.com',
            'password' => Hash::make('password'),
            'store_id' => $southStore->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $southCashier->assignRole('Cashier');
    }
}
