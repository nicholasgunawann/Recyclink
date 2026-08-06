<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan seeder PENTING karena ada dependency antar data:
     * 1. RolePermissionSeeder  - Harus pertama (roles & permissions)
     * 2. AdminUserSeeder       - Butuh roles sudah ada
     * 3. WasteCategorySeeder   - Independent
     * 4. DemoListingSeeder     - Butuh seller & kategori sudah ada
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            WasteCategorySeeder::class,
            DemoListingSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('  ✅  Recyclink Database Seeded Successfully!  ');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin Utama', 'admin@recyclink.com', 'password123'],
                ['Super Admin', 'admin@recyclink.id',  'Admin@Recyclink2026!'],
                ['Seller',      'seller@recyclink.id', 'Seller@Demo2026!'],
                ['Buyer',       'buyer@recyclink.id',  'Buyer@Demo2026!'],
            ]
        );
    }
}
