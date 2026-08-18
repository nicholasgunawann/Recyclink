<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use App\Exceptions\RecyclinkException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminWithdrawalController extends Controller implements HasMiddleware
{
    protected WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin',
        ];
    }

    // ponytail: list all withdrawal requests with search & status filters
    public function index(Request $request)
    {
        $query = Withdrawal::with(['user.sellerProfile', 'wallet', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('withdrawal_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('bank_account_number', 'like', "%{$search}%")
                  ->orWhere('bank_account_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $stats = [
            'total' => Withdrawal::count(),
            'pending' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'approved' => Withdrawal::where('status', Withdrawal::STATUS_APPROVED)->count(),
            'paid' => Withdrawal::where('status', Withdrawal::STATUS_PAID)->count(),
            'rejected' => Withdrawal::where('status', Withdrawal::STATUS_REJECTED)->count(),
            'total_paid_amount' => (float) Withdrawal::where('status', Withdrawal::STATUS_PAID)->sum('net_amount'),
        ];

        $withdrawals = $query->latest()->paginate(15)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    // ponytail: view withdrawal details
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['user.sellerProfile', 'wallet', 'approver']);
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    // ponytail: admin approves withdrawal
    public function approve(Withdrawal $withdrawal)
    {
        try {
            $this->withdrawalService->approveWithdrawal(auth()->user(), $withdrawal);
            return redirect()->back()->with('success', "Ajuan penarikan #{$withdrawal->withdrawal_number} berhasil disetujui dan siap ditransfer.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: admin rejects withdrawal and refunds balance to seller
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi untuk pemberitahuan ke penjual.',
        ]);

        try {
            $this->withdrawalService->rejectWithdrawal(auth()->user(), $withdrawal, $request->input('admin_note'));
            return redirect()->back()->with('success', "Ajuan penarikan #{$withdrawal->withdrawal_number} telah ditolak dan saldo telah dikembalikan ke dompet penjual.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: admin marks withdrawal as paid / transfer completed
    public function pay(Withdrawal $withdrawal)
    {
        try {
            $this->withdrawalService->markWithdrawalAsPaid(auth()->user(), $withdrawal);
            return redirect()->back()->with('success', "Penarikan #{$withdrawal->withdrawal_number} berhasil ditandai telah dibayarkan / transfer selesai.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
