<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exceptions\RecyclinkException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminUserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:admin',
        ];
    }

    // ponytail: list all users with eager loading
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // ponytail: show single user details
    public function show(User $user)
    {
        $user->load(['sellerProfile', 'buyerProfile']);
        return view('admin.users.show', compact('user'));
    }

    // ponytail: update user active/inactive/suspended status
    public function updateStatus(Request $request, User $user)
    {
        try {
            $status = $request->input('status', '');
            if (!in_array($status, [User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED, User::STATUS_PENDING])) {
                throw new RecyclinkException("Invalid status: {$status}");
            }
            $user->update(['status' => $status]);
            return redirect()->back()->with('success', 'User status updated successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
