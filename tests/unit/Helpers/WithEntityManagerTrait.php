<?php

namespace Tests\Helpers;

use Doctrine\ORM\EntityManager;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Repositories\PageCommentsRepository;
use Tests\App\Repositories\PagesRepository;
use Tests\App\Repositories\RolesRepository;
use Tests\App\Repositories\TagsRepository;
use Tests\App\Repositories\UsersRepository;

trait WithEntityManagerTrait
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

    public function pageRepo(): PagesRepository
    {
        return $this->em()->getRepository(Page::class);
    }

    public function pageCommentsRepo(): PageCommentsRepository
    {
        return $this->em()->getRepository(PageComment::class);
    }
}
