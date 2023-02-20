<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class PagePolicy
{
    public function show(User $user, Page $page): bool
    {
        return true;
    }

    public function updateRelationships(User $user, Page $page, string $relationshipClass): bool
    {
        return match ($relationshipClass) {
            User::class => $user->getRoles()->contains(Role::moderator()),
            default => false,
        };
    }

    public function showRelationships(User $user, Page $page, string $relatedResourceClass): bool
    {
        return match ($relatedResourceClass) {
            User::class => true,
            default => false
        };
    }
}
