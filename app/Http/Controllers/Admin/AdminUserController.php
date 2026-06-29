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
            $protectedEmails = ['admin@recyclink.id', 'admin@recyclink.com'];
            if (in_array($user->email, $protectedEmails) && $request->input('status') !== User::STATUS_ACTIVE) {
                throw new RecyclinkException("Anda tidak dapat mengubah status akun Super Admin atau Admin Utama.");
            }

            $status = $request->input('status', '');
            if (!in_array($status, [User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED, User::STATUS_PENDING])) {
                throw new RecyclinkException("Invalid status: {$status}");
            }
            
            $updateData = ['status' => $status];
            if ($request->has('rejection_reason')) {
                $updateData['rejection_reason'] = $request->input('rejection_reason');
            } elseif ($status === User::STATUS_ACTIVE) {
                // Clear rejection reason if activated
                $updateData['rejection_reason'] = null;
            }
            
            $user->update($updateData);
            return redirect()->back()->with('success', 'Status pengguna berhasil diperbarui.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: delete user from database
    public function destroy(User $user)
    {
        try {
            $currentUser = auth()->user();

            if ($user->id === $currentUser->id) {
                throw new RecyclinkException("Anda tidak dapat menghapus akun Anda sendiri.");
            }
            
            if ($user->email === 'admin@recyclink.id') {
                throw new RecyclinkException("Akun Super Admin tidak dapat dihapus.");
            }

            if ($user->email === 'admin@recyclink.com' && $currentUser->email !== 'admin@recyclink.id') {
                throw new RecyclinkException("Hanya Super Admin yang dapat menghapus akun Admin Utama.");
            }
            $user->forceDelete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus secara permanen dari sistem.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }
    }
}
