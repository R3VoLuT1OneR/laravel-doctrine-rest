<?php

namespace Tests\App\Policies;


use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class RolePolicy
{
    public function show(User $user, Role $role): bool
    {
        return $user->getRoles()->contains($role);
    }

    public function list(): bool
    {
        return false;
    }
}
