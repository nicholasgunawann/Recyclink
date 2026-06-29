<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Exceptions\RecyclinkException;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RegisterController extends Controller implements HasMiddleware
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['showChooseRoleForm', 'storeRole', 'resubmitVerification']),
        ];
    }

    // ponytail: render registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            return $this->redirectUser($user)->with('success', 'Pendaftaran berhasil!');
        } catch (RecyclinkException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ponytail: handle resubmission of user data after rejection
    public function resubmitVerification(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ];

        if ($user->hasRole('seller')) {
            $rules['business_name'] = 'required|string|max:255';
            $rules['business_type'] = 'nullable|string|max:100';
        } elseif ($user->hasRole('buyer')) {
            $rules['company_name'] = 'required|string|max:255';
            $rules['industry_type'] = 'nullable|string|max:100';
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'status' => \App\Models\User::STATUS_PENDING,
        ]);

        if ($user->hasRole('seller') && $user->sellerProfile) {
            $user->sellerProfile()->update([
                'business_name' => $validated['business_name'],
                'business_type' => $validated['business_type'] ?? 'Perorangan',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
            ]);
        } elseif ($user->hasRole('buyer') && $user->buyerProfile) {
            $user->buyerProfile()->update([
                'company_name' => $validated['company_name'],
                'industry_type' => $validated['industry_type'] ?? 'Lainnya',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
            ]);
        }

        return redirect()->route('verification.pending')->with('success', 'Formulir berhasil diperbarui! Mohon tunggu verifikasi selanjutnya.');
    }

    // ponytail: render choose role form
    public function showChooseRoleForm()
    {
        $user = request()->user();
        if ($user->roles->count() > 0) {
            return $this->redirectUser($user);
        }
        return view('auth.choose-role');
    }

    // ponytail: handle choose role submission
    public function storeRole(\Illuminate\Http\Request $request)
    {
        $request->validate(['role' => 'required|in:buyer,seller']);
        $user = $request->user();
        
        if ($user->roles->count() > 0) {
            return $this->redirectUser($user);
        }

        $user->assignRole($request->role);

        // Initialize default blank profile
        if ($request->role === 'seller') {
            $user->sellerProfile()->create([
                'business_name' => $user->name,
                'address' => '',
                'city' => '',
            ]);
        } elseif ($request->role === 'buyer') {
            $user->buyerProfile()->create([
                'company_name' => $user->name,
                'address' => '',
                'city' => '',
            ]);
        }

        return $this->redirectUser($user)->with('success', 'Peran berhasil dipilih.');
    }

    // ponytail: helper to redirect user based on role
    protected function redirectUser(User $user)
    {
        if ($user->status === User::STATUS_PENDING) {
            return redirect()->route('verification.pending');
        }
        if ($user->status === \App\Models\User::STATUS_INACTIVE || $user->status === \App\Models\User::STATUS_SUSPENDED) {
            return redirect()->route('verification.rejected');
        }
        if ($user->roles->count() === 0) {
            return redirect()->route('choose.role');
        }
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        } elseif ($user->isBuyer()) {
            return redirect()->route('buyer.dashboard');
        }
        
        return redirect()->route('home');
    }
}
