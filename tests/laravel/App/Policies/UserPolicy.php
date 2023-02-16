<?php namespace Tests\App\Policies;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserPolicy
{
    public function show(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function list(): bool
    {
        return false;
    }

    public function listRelated(User $user, User $resource, string $relatedResourceClass): bool
    {
        switch ($relatedResourceClass) {
            case Role::class:
                return $user === $resource;
        }

        return false;
    }
}
