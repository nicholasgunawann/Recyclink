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
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        }
        if ($user->isBuyer()) {
            return redirect()->route('buyer.dashboard');
        }
        return redirect()->route('home');
    }
}
