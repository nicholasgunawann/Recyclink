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
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'notification_type' => $type,
            'reference_id' => $referenceId,
            'is_read' => false,
        ]);
    }

    // ponytail: notify seller when a new order is created
    public function notifyOrderCreated(Order $order): void
    {
        $seller = $order->seller;
        if ($seller) {
            $this->sendToUser(
                $seller,
                "New Order Received",
                "You have received a new order {$order->order_code}.",
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
            $this->sendToUser(
                $buyer,
                "Order Status Updated",
                "Your order {$order->order_code} status is now: " . strtoupper($order->order_status),
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
                "Order Paid",
                "Payment for order {$order->order_code} has been successfully verified.",
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
            $status = strtoupper($listing->verification_status);
            $note = $listing->admin_note ? " Note: {$listing->admin_note}" : "";
            
            $this->sendToUser(
                $seller,
                "Listing Verification {$status}",
                "Your listing '{$listing->title}' verification status is {$status}.{$note}",
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
            $note = $listing->admin_note ? " Reason: {$listing->admin_note}" : "";
            
            $this->sendToUser(
                $seller,
                "Listing Deactivated",
                "Your listing '{$listing->title}' has been deactivated by the administrator.{$note}",
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
                "New Complaint Filed",
                "A complaint has been filed against you regarding order #{$complaint->order->order_code}.",
                "complaint",
                $complaint->id
            );
        }

        // Notify all admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $this->sendToUser(
                $admin,
                "New Complaint Filed",
                "A new complaint #{$complaint->complaint_number} has been filed for order #{$complaint->order->order_code}.",
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
                "Withdrawal Requested",
                "Your withdrawal request of ID {$withdrawal->withdrawal_number} for amount " . number_format($withdrawal->amount, 2) . " is pending approval.",
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
                "Withdrawal Approved",
                "Your withdrawal request {$withdrawal->withdrawal_number} has been approved and is being processed.",
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
            $note = $withdrawal->admin_note ? " Reason: {$withdrawal->admin_note}" : "";
            $this->sendToUser(
                $seller,
                "Withdrawal Rejected",
                "Your withdrawal request {$withdrawal->withdrawal_number} has been rejected.{$note}",
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
                "Withdrawal Completed",
                "Your withdrawal request {$withdrawal->withdrawal_number} has been paid successfully.",
                "withdrawal",
                $withdrawal->id
            );
        }
    }
}
