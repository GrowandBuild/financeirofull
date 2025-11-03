<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Purchase $purchase): bool
    {
        return $purchase->user_id === $user->id;
    }
    
    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Purchase $purchase): bool
    {
        return $purchase->user_id === $user->id;
    }
}

