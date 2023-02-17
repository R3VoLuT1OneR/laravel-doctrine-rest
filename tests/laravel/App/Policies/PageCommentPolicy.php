<?php

namespace Tests\App\Policies;

use Tests\App\Entities\PageComment;
use Tests\App\Entities\User;

class PageCommentPolicy
{
    public function show(User $user, PageComment $comment): bool
    {
        return true;
    }
}