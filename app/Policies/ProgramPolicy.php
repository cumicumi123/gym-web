<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function view(User $user, Program $program): bool
    {
        return $user->id === $program->user_id;
    }

    public function update(User $user, Program $program): bool
    {
        return $user->id === $program->user_id;
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->id === $program->user_id;
    }
}
