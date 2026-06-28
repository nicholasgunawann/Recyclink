<?php
$user = App\Models\User::firstOrCreate(
    ['email' => 'admin@recyclink.com'],
    [
        'name' => 'Admin Utama',
        'password' => bcrypt('password123'),
        'status' => 'active',
        'phone_number' => '081234567890',
        'email_verified_at' => now(),
    ]
);
$role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
$user->assignRole($role);
echo "\n[SUCCESS] Admin created!\nEmail: admin@recyclink.com\nPassword: password123\n";
