<?php namespace Tests\App\Policies;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserPolicy
{
    public function show(User $user, User $resource): bool
    {
        return true;
    }

    public function list(): bool
    {
        return false;
    }

    public function create(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function update(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function remove(User $user, User $resource): bool
    {
        return $user === $resource;
    }

    public function assignRole(User $user, User $resource, Role $role): bool
    {
        return false;
    }

    public function removeRelationships(User $user, User $resource, string $relationshipClass): bool
    {
        return match ($relationshipClass) {
            default => false
        };
    }

    public function listRelationships(User $user, User $resource, string $relatedResourceClass): bool
    {
        return match ($relatedResourceClass) {
            Role::class => $user === $resource,
            default => false,
        };
    }
}
