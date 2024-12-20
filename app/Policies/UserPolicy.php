<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user->type === 'A';
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user)
    {
        if($authUser->id === $user->id && $authUser->type === 'A'){
            return false;
        }
        
        return $authUser->id === $user->id || $authUser->type === 'A';
    }

}
