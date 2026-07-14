<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\WasteListing;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Withdrawal;

class NotificationService
{
    // ponytail: send a general notification to a user
    public function sendToUser(User $user, string $title, string $message, string $type, $referenceId = null): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'notification_type' => $type,
            'reference_id' => $referenceId,
            'is_read' => false,
        ]);

        // Invalidate notification cache so dropdown shows the new notification
        \Illuminate\Support\Facades\Cache::forget("notif_unread_{$user->id}");
        \Illuminate\Support\Facades\Cache::forget("notif_recent_{$user->id}");

        return $notification;
    }

    // ponytail: notify seller when a new order is created
    public function notifyOrderCreated(Order $order): void
    {
        $seller = $order->seller;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "Pesanan Baru Diterima",
                "Anda menerima pesanan baru dengan kode {$order->order_code}.",
                "order",
                $order->id
            );
        }
    }

    // ponytail: notify buyer when order status is changed
    public function notifyOrderStatusChanged(Order $order): void
    {
        $buyer = $order->buyer;
        if ($buyer) {
            $statusMap = [
                'pending'    => 'Menunggu Konfirmasi',
                'accepted'   => 'Diterima',
                'processing' => 'Diproses',
                'completed'  => 'Selesai',
                'cancelled'  => 'Dibatalkan',
                'rejected'   => 'Ditolak',
            ];
            $statusLabel = $statusMap[$order->order_status] ?? strtoupper($order->order_status);
            $this->sendToUser(
                $buyer,
                "Status Pesanan Diperbarui",
                "Status pesanan {$order->order_code} Anda sekarang: {$statusLabel}.",
                "order",
                $order->id
            );
        }
    }

    // ponytail: notify seller when payment is successful
    public function notifyPaymentSuccess(Order $order): void
    {
        $seller = $order->seller;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "Pembayaran Diterima",
                "Pembayaran untuk pesanan {$order->order_code} telah berhasil diverifikasi.",
                "payment",
                $order->id
            );
        }
    }

    // ponytail: notify seller when listing verification is approved/rejected
    public function notifyListingVerified(WasteListing $listing): void
    {
        $seller = $listing->seller;
        if ($seller) {
            $statusMap = [
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'pending'  => 'Menunggu Verifikasi',
            ];
            $statusLabel = $statusMap[$listing->verification_status] ?? strtoupper($listing->verification_status);
            $note = $listing->admin_note ? " Catatan: {$listing->admin_note}" : "";

            $this->sendToUser(
                $seller,
                "Verifikasi Listing {$statusLabel}",
                "Listing '{$listing->title}' Anda telah {$statusLabel}.{$note}",
                "listing",
                $listing->id
            );
        }
    }

    // ponytail: notify seller when listing is deactivated by admin
    public function notifyListingDeactivated(WasteListing $listing): void
    {
        $seller = $listing->seller;
        if ($seller) {
            $note = $listing->admin_note ? " Alasan: {$listing->admin_note}" : "";

            $this->sendToUser(
                $seller,
                "Listing Dinonaktifkan",
                "Listing '{$listing->title}' Anda telah dinonaktifkan oleh administrator.{$note}",
                "listing",
                $listing->id
            );
        }
    }

    // ponytail: notify admin or respondent of complaint
    public function notifyComplaintCreated(Complaint $complaint): void
    {
        $respondent = $complaint->respondent;
        if ($respondent) {
            $this->sendToUser(
                $respondent,
                "Keluhan Baru Diajukan",
                "Terdapat keluhan yang diajukan terhadap Anda terkait pesanan #{$complaint->order->order_code}.",
                "complaint",
                $complaint->id
            );
        }

        // Notify all admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $this->sendToUser(
                $admin,
                "Keluhan Baru Masuk",
                "Keluhan baru #{$complaint->complaint_number} telah diajukan untuk pesanan #{$complaint->order->order_code}.",
                "complaint",
                $complaint->id
            );
        }
    }

    // ponytail: notify seller when a withdrawal is requested
    public function notifyWithdrawalRequested(Withdrawal $withdrawal): void
    {
        $seller = $withdrawal->user;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "Permintaan Penarikan Dana Diterima",
                "Permintaan penarikan dana #{$withdrawal->withdrawal_number} sebesar Rp " . number_format((float) $withdrawal->amount, 0, ',', '.') . " sedang menunggu persetujuan.",
                "withdrawal",
                $withdrawal->id
            );
        }
    }

    // ponytail: notify seller when a withdrawal is approved
    public function notifyWithdrawalApproved(Withdrawal $withdrawal): void
    {
        $seller = $withdrawal->user;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "Penarikan Dana Disetujui",
                "Permintaan penarikan dana #{$withdrawal->withdrawal_number} Anda telah disetujui dan sedang diproses.",
                "withdrawal",
                $withdrawal->id
            );
        }
    }

    // ponytail: notify seller when a withdrawal is rejected
    public function notifyWithdrawalRejected(Withdrawal $withdrawal): void
    {
        $seller = $withdrawal->user;
        if ($seller) {
            $note = $withdrawal->admin_note ? " Alasan: {$withdrawal->admin_note}" : "";
            $this->sendToUser(
                $seller,
                "Penarikan Dana Ditolak",
                "Permintaan penarikan dana #{$withdrawal->withdrawal_number} Anda telah ditolak.{$note}",
                "withdrawal",
                $withdrawal->id
            );
        }
    }

    // ponytail: notify seller when a withdrawal is paid
    public function notifyWithdrawalPaid(Withdrawal $withdrawal): void
    {
        $seller = $withdrawal->user;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "Penarikan Dana Berhasil",
                "Permintaan penarikan dana #{$withdrawal->withdrawal_number} Anda telah berhasil dibayarkan.",
                "withdrawal",
                $withdrawal->id
            );
        }
    }
}
