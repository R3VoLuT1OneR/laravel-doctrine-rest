<?php

namespace Tests\Helpers;

use Doctrine\ORM\EntityManager;
use Tests\App\Entities\Blog;
use Tests\App\Entities\BlogComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\Tag;
use Tests\App\Entities\User;
use Tests\App\Repositories\BlogCommentsRepository;
use Tests\App\Repositories\BlogsRepository;
use Tests\App\Repositories\RolesRepository;
use Tests\App\Repositories\TagsRepository;
use Tests\App\Repositories\UsersRepository;

trait WithEntityManager
{
    protected EntityManager $em;

    public function em(): EntityManager
    {
        return $this->em;
    }

    public function usersRepo(): UsersRepository
    {
        return $this->em()->getRepository(User::class);
    }

    public function rolesRepo(): RolesRepository
    {
        return $this->em()->getRepository(Role::class);
    }

    public function tagsRepo(): TagsRepository
    {
        return $this->em()->getRepository(Tag::class);
    }

    public function blogsRepo(): BlogsRepository
    {
        return $this->em()->getRepository(Blog::class);
    }

    public function blogCommentsRepo(): BlogCommentsRepository
    {
        return $this->em()->getRepository(BlogComment::class);
    }
}
