<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Exceptions\RecyclinkException;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoginController extends Controller implements HasMiddleware
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
            new Middleware('auth', only: ['logout']),
        ];
    }

    // ponytail: render login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ponytail: handle login attempt
    public function login(LoginRequest $request)
    {
        try {
            $this->authService->login($request->validated());
            return $this->redirectUser(auth()->user());
        } catch (RecyclinkException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ponytail: handle logout
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }

    // ponytail: helper to redirect user based on role
    protected function redirectUser(User $user)
    {
        if ($user->status === User::STATUS_PENDING) {
            return redirect()->route('verification.pending');
        }
        if ($user->status === User::STATUS_INACTIVE || $user->status === User::STATUS_SUSPENDED) {
            return redirect()->route('verification.rejected');
        }
        // If active but hasn't seen the success page yet
        if ($user->status === User::STATUS_ACTIVE && $user->email_verified_at === null) {
            return redirect()->route('verification.pending');
        }
        if ($user->roles->count() === 0) {
            return redirect()->route('choose.role');
        }
        
        // Admin directly to dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        $fallback = route('home');
        if ($user->isSeller()) {
            $fallback = route('seller.dashboard');
        } elseif ($user->isBuyer()) {
            $fallback = route('buyer.dashboard');
        }
        
        return redirect()->intended($fallback);
    }
}
