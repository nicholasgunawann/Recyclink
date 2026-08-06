<?php

namespace Database\Seeders;

use App\Models\SellerWallet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin Utama ──────────────────────────────────────────────────────
        $adminUtama = User::updateOrCreate(
            ['email' => 'admin@recyclink.com'],
            [
                'name'              => 'Admin Utama',
                'password'          => Hash::make('password123'),
                'phone_number'      => '081234567899',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminUtama->assignRole('admin');

        $this->command->info('Admin Utama created: admin@recyclink.com');

        // ─── Super Admin ─────────────────────────────────────────────────────
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@recyclink.id'],
            [
                'name'              => 'Super Admin Recyclink',
                'password'          => Hash::make('Admin@Recyclink2026!'),
                'phone_number'      => '081234567890',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('admin');

        $this->command->info('Super Admin created: admin@recyclink.id');

        // ─── Demo Seller ──────────────────────────────────────────────────────
        $demoSeller = User::updateOrCreate(
            ['email' => 'seller@recyclink.id'],
            [
                'name'              => 'Demo Seller UMKM',
                'password'          => Hash::make('Seller@Demo2026!'),
                'phone_number'      => '081298765432',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $demoSeller->assignRole('seller');

        \App\Models\SellerProfile::updateOrCreate(
            ['user_id' => $demoSeller->id],
            [
                'business_name'       => 'CV. Daur Ulang Nusantara',
                'business_type'       => 'CV',
                'description'         => 'Kami adalah perusahaan daur ulang limbah yang telah beroperasi sejak 2015. Melayani pembelian limbah plastik, kertas, logam, dan elektronik dari seluruh Indonesia.',
                'address'             => 'Jl. Industri Raya No. 45, Kawasan Industri Pulogadung',
                'city'                => 'Jakarta',
                'province'            => 'DKI Jakarta',
                'postal_code'         => '13920',
                'latitude'            => -6.1801,
                'longitude'           => 106.9010,
                'bank_name'           => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name'     => 'CV Daur Ulang Nusantara',
                'verification_status' => 'verified',
                'verified_at'         => now()->subMonths(3),
            ]
        );

        SellerWallet::firstOrCreate(['seller_id' => $demoSeller->id], [
            'balance'          => 1500000,
            'pending_balance'  => 250000,
            'total_earned'     => 5000000,
            'total_withdrawn'  => 3250000,
        ]);

        $this->command->info('Demo Seller created: seller@recyclink.id');

        // ─── Demo Buyer ───────────────────────────────────────────────────────
        $demoBuyer = User::updateOrCreate(
            ['email' => 'buyer@recyclink.id'],
            [
                'name'              => 'Demo Buyer Industri',
                'password'          => Hash::make('Buyer@Demo2026!'),
                'phone_number'      => '082187654321',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $demoBuyer->assignRole('buyer');

        \App\Models\BuyerProfile::updateOrCreate(
            ['user_id' => $demoBuyer->id],
            [
                'company_name'  => 'PT. Maju Industri Mandiri',
                'address'       => 'Jl. Gatot Subroto Kav. 51-53',
                'city'          => 'Jakarta',
                'province'      => 'DKI Jakarta',
                'postal_code'   => '12950',
                'industry_type' => 'Manufaktur',
            ]
        );

        $this->command->info('Demo Buyer created: buyer@recyclink.id');
    }
}
