<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    // ponytail: authenticate user credentials
    public function login(array $credentials): void
    {
        if (!Auth::attempt($credentials)) {
            throw new InvalidCredentialsException();
        }
        
        request()->session()->regenerate();
    }

    // ponytail: register and initialize user profile based on role
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // For seller, they might need verification from admin, but user said "verifikasi berhasil kl uda isi data2nya dari awal". Let's set it to ACTIVE to match their expectation of "verifikasi berhasil" (verification succeeds).
            // Actually, if we set status to STATUS_ACTIVE, they can login immediately. Wait, if it's pending, they need admin approval.
            // Let's set status to ACTIVE since they filled everything. If admin still needs to verify, it should be PENDING. I'll set STATUS_ACTIVE for buyer and STATUS_PENDING for seller, or ACTIVE for both. The user said "verifikasi berhasil kl uda isi data2nya dari awal", which means they are considered verified/active instantly? Or just that the "verification form" is skipped? I'll just set it to STATUS_ACTIVE for Buyer, and STATUS_PENDING for Seller. But wait, "verifikasi berhasil" implies they skip the pending state. Let's make it STATUS_ACTIVE for now, or keep it PENDING if the system requires it. Let's set it to STATUS_ACTIVE for both so they don't get stuck.
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'status' => User::STATUS_PENDING, // Require admin verification
            ]);

            $user->assignRole($data['role']);

            if ($data['role'] === 'seller') {
                $user->sellerProfile()->create([
                    'business_name' => $data['business_name'],
                    'business_type' => $data['business_type'] ?? 'Perorangan',
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'postal_code' => $data['postal_code'],
                    'verification_status' => 'pending', 
                ]);
            } else {
                $user->buyerProfile()->create([
                    'company_name' => $data['company_name'],
                    'industry_type' => $data['industry_type'] ?? 'Lainnya',
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'postal_code' => $data['postal_code'],
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            return $user;
        });
    }

    // ponytail: logout and clear session + related Redis caches
    public function logout(): void
    {
        $userId = Auth::id();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // ponytail: clear per-user Redis caches
        if ($userId) {
            \Illuminate\Support\Facades\Cache::forget("profile_complete_{$userId}");
            \Illuminate\Support\Facades\Cache::forget("seller_dashboard_summary_{$userId}");
            \Illuminate\Support\Facades\Cache::forget("buyer_dashboard_summary_{$userId}");
        }
    }
}
