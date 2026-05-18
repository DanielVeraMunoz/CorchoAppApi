<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->role === 'admin';
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->role === 'admin';
    }

    public function complete(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->role === 'admin';
    }

    public function reopen(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->role === 'admin';
    }

}
