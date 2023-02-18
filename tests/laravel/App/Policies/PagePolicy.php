<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\User;

class PagePolicy
{
    public function show(User $user, Page $page): bool
    {
        return true;
    }

    public function showRelated(User $user, Page $page, string $relatedResourceClass): bool
    {
        return match ($relatedResourceClass) {
            User::class => true,
            default => false
        };
    }
}