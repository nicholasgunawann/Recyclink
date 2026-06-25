<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Complaint;
use App\Exceptions\UnauthorizedBusinessActionException;
use App\Exceptions\ComplaintNotAllowedException;
use App\Exceptions\InvalidOrderStatusException;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    protected NotificationService $notificationService;
    protected ActivityLogService $activityLogService;
    protected FileUploadService $fileUploadService;

    public function __construct(
        NotificationService $notificationService,
        ActivityLogService $activityLogService,
        FileUploadService $fileUploadService
    ) {
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
        $this->fileUploadService = $fileUploadService;
    }

    // ponytail: create complaint for an order (allowed within 7 days of order creation/completion)
    public function createComplaint(User $user, Order $order, array $data): Complaint
    {
        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            throw new ComplaintNotAllowedException("You must be a participant of this order to file a complaint.");
        }

        // Check if order is older than 7 days
        $completionDate = $order->cancelled_at ?: ($order->updated_at ?: $order->created_at);
        if (now()->diffInDays($completionDate) > 7) {
            throw new ComplaintNotAllowedException("Complaints are only allowed within 7 days.");
        }

        $respondentId = ($order->buyer_id === $user->id) ? $order->seller_id : $order->buyer_id;

        return DB::transaction(function () use ($user, $order, $respondentId, $data) {
            $evidenceUrl = null;
            if (isset($data['evidence']) && $data['evidence'] instanceof \Illuminate\Http\UploadedFile) {
                // ponytail: upload evidence file via FileUploadService
                $evidenceUrl = $this->fileUploadService->upload($data['evidence'], 'complaints');
            } elseif (isset($data['evidence_url'])) {
                $evidenceUrl = $data['evidence_url'];
            }

            $complaint = Complaint::create([
                'order_id' => $order->id,
                'complainant_id' => $user->id,
                'respondent_id' => $respondentId,
                'complaint_number' => 'CMP-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                'subject' => $data['subject'] ?? 'Order Dispute',
                'complaint_type' => $data['complaint_type'] ?? 'other',
                'description' => $data['description'],
                'evidence_url' => $evidenceUrl,
                'status' => Complaint::STATUS_OPEN,
            ]);

            // Log activity
            $this->activityLogService->log(
                'complaint.create',
                'complaints',
                $complaint->id,
                "Complaint filed by user {$user->name}."
            );

            // Send notification to respondent and admin
            $this->notificationService->notifyComplaintCreated($complaint);

            return $complaint;
        });
    }

    // ponytail: admin processes the complaint (status under_review)
    public function processComplaint(User $admin, Complaint $complaint, array $data): Complaint
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can process complaints.");
        }

        if ($complaint->status !== Complaint::STATUS_OPEN) {
            throw new InvalidOrderStatusException("Complaint is already under review or resolved.");
        }

        return DB::transaction(function () use ($admin, $complaint) {
            $complaint->update([
                'status' => Complaint::STATUS_UNDER_REVIEW,
                'admin_id' => $admin->id,
            ]);

            // Log activity
            $this->activityLogService->log(
                'complaint.process',
                'complaints',
                $complaint->id,
                "Complaint marked as under review by Admin: {$admin->name}."
            );

            return $complaint;
        });
    }

    // ponytail: admin resolves the complaint
    public function resolveComplaint(User $admin, Complaint $complaint, string $note): Complaint
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can resolve complaints.");
        }

        if (empty($note)) {
            throw new InvalidOrderStatusException("Resolution note is required.");
        }

        if ($complaint->status === Complaint::STATUS_RESOLVED) {
            throw new InvalidOrderStatusException("Complaint is already resolved.");
        }

        return DB::transaction(function () use ($admin, $complaint, $note) {
            $complaint->update([
                'status' => Complaint::STATUS_RESOLVED,
                'resolution_note' => $note,
                'resolved_at' => now(),
                'admin_id' => $admin->id,
            ]);

            // Log activity
            $this->activityLogService->log(
                'complaint.resolve',
                'complaints',
                $complaint->id,
                "Complaint resolved by Admin. Note: {$note}"
            );

            return $complaint;
        });
    }

    // ponytail: admin rejects the complaint
    public function rejectComplaint(User $admin, Complaint $complaint, string $note): Complaint
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can reject complaints.");
        }

        if (empty($note)) {
            throw new InvalidOrderStatusException("Resolution note is required.");
        }

        if ($complaint->status === Complaint::STATUS_RESOLVED) {
            throw new InvalidOrderStatusException("Cannot reject an already resolved complaint.");
        }

        return DB::transaction(function () use ($admin, $complaint, $note) {
            $complaint->update([
                'status' => Complaint::STATUS_REJECTED,
                'resolution_note' => $note,
                'resolved_at' => now(),
                'admin_id' => $admin->id,
            ]);

            // Log activity
            $this->activityLogService->log(
                'complaint.reject',
                'complaints',
                $complaint->id,
                "Complaint rejected by Admin. Note: {$note}"
            );

            return $complaint;
        });
    }
}
