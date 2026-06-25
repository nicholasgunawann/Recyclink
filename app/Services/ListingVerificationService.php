<?php

namespace App\Services;

use App\Models\User;
use App\Models\WasteListing;
use App\Models\ActivityLog;
use App\Exceptions\UnauthorizedBusinessActionException;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\ListingNotApprovedException;
use Illuminate\Support\Facades\DB;

class ListingVerificationService
{
    protected NotificationService $notificationService;
    protected ActivityLogService $activityLogService;

    public function __construct(NotificationService $notificationService, ActivityLogService $activityLogService)
    {
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
    }

    // ponytail: approve waste listing (admin only)
    public function approveListing(User $admin, WasteListing $listing, ?string $note = null): WasteListing
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can approve listings.");
        }

        if ($listing->verification_status === WasteListing::VERIFICATION_APPROVED) {
            throw new InvalidOrderStatusException("Listing is already approved.");
        }

        return DB::transaction(function () use ($admin, $listing, $note) {
            $listing->update([
                'verification_status' => WasteListing::VERIFICATION_APPROVED,
                'availability_status' => WasteListing::AVAILABILITY_AVAILABLE,
                'published_at' => now(),
                'admin_note' => $note,
            ]);

            // Log activity
            $this->activityLogService->log(
                'listing.approve',
                'waste_listings',
                $listing->id,
                "Listing approved by Admin: {$listing->title}"
            );

            // Send notification
            $this->notificationService->notifyListingVerified($listing);

            return $listing;
        });
    }

    // ponytail: reject waste listing (admin only, note required)
    public function rejectListing(User $admin, WasteListing $listing, string $note): WasteListing
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can reject listings.");
        }

        if (empty($note)) {
            throw new InvalidOrderStatusException("Admin note is required for rejections.");
        }

        return DB::transaction(function () use ($admin, $listing, $note) {
            $listing->update([
                'verification_status' => WasteListing::VERIFICATION_REJECTED,
                'availability_status' => WasteListing::AVAILABILITY_INACTIVE,
                'admin_note' => $note,
            ]);

            // Log activity
            $this->activityLogService->log(
                'listing.reject',
                'waste_listings',
                $listing->id,
                "Listing rejected by Admin. Note: {$note}"
            );

            // Send notification
            $this->notificationService->notifyListingVerified($listing);

            return $listing;
        });
    }

    // ponytail: deactivate a listing (admin only)
    public function deactivateListing(User $admin, WasteListing $listing, ?string $note = null): WasteListing
    {
        if (!$admin->isAdmin()) {
            throw new UnauthorizedBusinessActionException("Only administrators can deactivate listings.");
        }

        return DB::transaction(function () use ($admin, $listing, $note) {
            $listing->update([
                'availability_status' => WasteListing::AVAILABILITY_INACTIVE,
                'admin_note' => $note ?: 'Deactivated by administrator.',
            ]);

            // Log activity
            $this->activityLogService->log(
                'listing.deactivate',
                'waste_listings',
                $listing->id,
                "Listing deactivated by Admin. Note: {$note}"
            );

            // Send notification
            $this->notificationService->notifyListingDeactivated($listing);

            return $listing;
        });
    }
}
