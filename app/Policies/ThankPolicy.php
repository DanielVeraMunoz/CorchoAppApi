<?php

namespace App\Policies;

use App\Models\Thank;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThankPolicy
{
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Thank $thank): bool
    {
        return $user->id === $thank->giver_id || $user->role === 'admin';
    }

}
