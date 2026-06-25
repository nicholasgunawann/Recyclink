<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Complaint;
use App\Models\Order;

class ComplaintPolicy
{
    // ponytail: allowed to view if admin or order participant
    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $complaint->complainant_id === $user->id 
            || $complaint->respondent_id === $user->id;
    }

    // ponytail: buyer or seller associated with the order can create complaint
    public function create(User $user, Order $order): bool
    {
        return $order->buyer_id === $user->id 
            || $order->seller_id === $user->id;
    }

    // ponytail: only admins can process, resolve, or reject complaints
    public function process(User $user): bool
    {
        return $user->isAdmin();
    }
}
