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
            return redirect()->route('verification.pending')->with('success', 'Pendaftaran berhasil! Akun Anda sedang diverifikasi.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ponytail: handle resubmission of user data after rejection
    public function resubmitVerification(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => \App\Models\User::STATUS_PENDING,
        ]);

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
        $fallback = route('home');
        if ($user->isAdmin()) {
            $fallback = route('admin.dashboard');
        } elseif ($user->isSeller()) {
            $fallback = route('seller.dashboard');
        } elseif ($user->isBuyer()) {
            $fallback = route('buyer.dashboard');
        }
        
        return redirect()->intended($fallback);
    }
}
