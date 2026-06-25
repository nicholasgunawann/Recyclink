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
            new Middleware('guest'),
        ];
    }

    // ponytail: render registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // ponytail: handle registration attempt
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            return $this->redirectUser($user)->with('success', 'Pendaftaran berhasil!');
        } catch (RecyclinkException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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
