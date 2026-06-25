<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EducationContent;

class EducationContentPolicy
{
    // ponytail: guests/buyers/sellers can only view if published; admins can view anything
    public function view(?User $user, EducationContent $content): bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        return $content->status === 'published';
    }

    // ponytail: admin-only actions
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user): bool
    {
        return $user->isAdmin();
    }

    public function archive(User $user): bool
    {
        return $user->isAdmin();
    }
}
