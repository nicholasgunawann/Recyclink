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
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'status' => User::STATUS_ACTIVE,
            ]);

            // Assign Spatie permission role
            $user->assignRole($data['role']);

            // Initialize default blank profile
            if ($data['role'] === 'seller') {
                $user->sellerProfile()->create([
                    'business_name' => $data['name'],
                    'address' => '',
                    'city' => '',
                ]);
            } elseif ($data['role'] === 'buyer') {
                $user->buyerProfile()->create([
                    'company_name' => $data['name'],
                    'address' => '',
                    'city' => '',
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            return $user;
        });
    }

    // ponytail: logout and clear session
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
