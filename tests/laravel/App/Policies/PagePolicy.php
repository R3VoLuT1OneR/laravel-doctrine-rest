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
}