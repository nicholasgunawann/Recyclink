<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Exceptions\RecyclinkException;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\UnauthorizedBusinessActionException;

class ReviewService
{
    // ponytail: create review for completed order
    public function createReview(User $reviewer, Order $order, array $data): Review
    {
        if ($order->order_status !== Order::STATUS_COMPLETED) {
            throw new InvalidOrderStatusException("Reviews can only be created for completed orders.");
        }

        if ($order->buyer_id !== $reviewer->id && $order->seller_id !== $reviewer->id) {
            throw new UnauthorizedBusinessActionException("You must be a participant of the order to review it.");
        }

        $existing = Review::where('order_id', $order->id)
            ->where('reviewer_id', $reviewer->id)
            ->first();

        if ($existing) {
            throw new RecyclinkException("You have already reviewed this order.");
        }

        $rating = (int) $data['rating'];
        if ($rating < 1 || $rating > 5) {
            throw new RecyclinkException("Rating must be between 1 and 5.");
        }

        $firstItem = $order->items()->first();

        $reviewedUserId = ($order->buyer_id === $reviewer->id) ? $order->seller_id : $order->buyer_id;

        return Review::create([
            'order_id' => $order->id,
            'reviewer_id' => $reviewer->id,
            'reviewed_user_id' => $reviewedUserId,
            'rating' => $rating,
            'review_text' => $data['review_text'] ?? $data['comment'] ?? null,
            'listing_id' => $firstItem ? $firstItem->listing_id : null,
            'is_anonymous' => $data['is_anonymous'] ?? false,
        ]);
    }
}
