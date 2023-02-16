<?php namespace Tests\App\Policies;

use Tests\App\Entities\User;

class UserPolicy
{
    public function show(User $user, User $resource): bool
    {
        return $user === $resource;
    }
}
