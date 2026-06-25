<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\StoreReviewRequest;
use App\Services\ReviewService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerReviewController extends Controller implements HasMiddleware
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:buyer',
        ];
    }

    // ponytail: store a new order review
    public function store(StoreReviewRequest $request, Order $order)
    {
        try {
            $this->reviewService->createReview(auth()->user(), $order, $request->validated());
            return redirect()->back()->with('success', 'Review submitted successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
