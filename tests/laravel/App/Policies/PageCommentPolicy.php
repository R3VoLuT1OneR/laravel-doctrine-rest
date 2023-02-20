<?php

namespace Tests\App\Policies;

use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class PageCommentPolicy
{
    public function show(User $user, PageComment $comment): bool
    {
        return true;
    }

    public function showRelationships(User $user, PageComment $comment, string $relatedResourceClass): bool
    {
        return false;
    }
}
