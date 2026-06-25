<?php

namespace App\Services;

use App\Models\User;
use App\Models\Withdrawal;
use App\Exceptions\UnauthorizedBusinessActionException;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\InsufficientWalletBalanceException;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    protected WalletService $walletService;
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;

    public function __construct(
        WalletService $walletService,
        ActivityLogService $activityLogService,
        NotificationService $notificationService
    ) {
        $this->walletService = $walletService;
        $this->activityLogService = $activityLogService;
        $this->notificationService = $notificationService;
    }

    // ponytail: seller requests a new withdrawal
    public function requestWithdrawal(User $seller, array $data): Withdrawal
    {
        if (!$seller->isSeller()) {
            throw new UnauthorizedBusinessActionException("Only sellers can request withdrawals.");
        }

        $wallet = $this->walletService->getWallet($seller);
        $amount = (float) $data['amount'];

        if (!$wallet->canWithdraw($amount)) {
            throw new InsufficientWalletBalanceException("Insufficient wallet balance.");
        }

        return DB::transaction(function () use ($seller, $wallet, $amount, $data) {
            // debit() internally decrements balance and records a transaction
            $this->walletService->withdraw($seller, $amount, "Withdrawal request.");

            $withdrawal = Withdrawal::create([
                'wallet_id' => $wallet->id,
                'user_id' => $seller->id,
                'amount' => $amount,
                'net_amount' => $amount,
                'admin_fee' => 0.00,
                'bank_name' => $data['bank_name'],
                'bank_account_number' => $data['bank_account_number'] ?? $data['account_number'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? $data['account_holder_name'] ?? null,
                'status' => Withdrawal::STATUS_PENDING,
                'withdrawal_number' => 'WD-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
            ]);

            // Log activity
            $this->activityLogService->log(
                'withdrawal.request',
                'withdrawals',
                $withdrawal->id,
                "Withdrawal requested for amount: {$amount}"
            );

            // Send notification
            $this->notificationService->notifyWithdrawalRequested($withdrawal);

            return $withdrawal;
        });
    }

    // ponytail: admin approves the withdrawal request
    public function approveWithdrawal(User $admin, Withdrawal $withdrawal): Withdrawal
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can approve withdrawals.");
        }

        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            throw new InvalidOrderStatusException("Withdrawal request is not pending.");
        }

        return DB::transaction(function () use ($admin, $withdrawal) {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'processed_at' => now(),
            ]);

            // Log activity
            $this->activityLogService->log(
                'withdrawal.approve',
                'withdrawals',
                $withdrawal->id,
                "Withdrawal request approved."
            );

            // Send notification
            $this->notificationService->notifyWithdrawalApproved($withdrawal);

            return $withdrawal;
        });
    }

    // ponytail: admin rejects withdrawal and refunds balance to seller wallet
    public function rejectWithdrawal(User $admin, Withdrawal $withdrawal, string $note): Withdrawal
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can reject withdrawals.");
        }

        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            throw new InvalidOrderStatusException("Withdrawal request is not pending.");
        }

        return DB::transaction(function () use ($admin, $withdrawal, $note) {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_REJECTED,
                'approved_by' => $admin->id,
                'admin_note' => $note,
                'processed_at' => now(),
            ]);

            // Refund using WalletService credit
            $this->walletService->addEarnings(
                $withdrawal->user,
                (float) $withdrawal->amount,
                null,
                "Refund for rejected withdrawal {$withdrawal->withdrawal_number}."
            );

            // Log activity
            $this->activityLogService->log(
                'withdrawal.reject',
                'withdrawals',
                $withdrawal->id,
                "Withdrawal request rejected. Note: {$note}"
            );

            // Send notification
            $this->notificationService->notifyWithdrawalRejected($withdrawal);

            return $withdrawal;
        });
    }

    // ponytail: mark withdrawal as paid (admin finishes payout process)
    public function markWithdrawalAsPaid(User $admin, Withdrawal $withdrawal): Withdrawal
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can mark withdrawals as paid.");
        }

        if ($withdrawal->status !== Withdrawal::STATUS_APPROVED) {
            throw new InvalidOrderStatusException("Withdrawal must be approved first.");
        }

        return DB::transaction(function () use ($admin, $withdrawal) {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_PAID,
                'processed_at' => now(),
                'admin_note' => $withdrawal->admin_note . ' [Paid]',
            ]);

            // Log activity
            $this->activityLogService->log(
                'withdrawal.pay',
                'withdrawals',
                $withdrawal->id,
                "Withdrawal marked as completed and paid."
            );

            // Send notification
            $this->notificationService->notifyWithdrawalPaid($withdrawal);

            return $withdrawal;
        });
    }
}
