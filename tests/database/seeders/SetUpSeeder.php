<?php

namespace Database\Seeders;

use Doctrine\ORM\EntityManager;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class SetUpSeeder
{
    public function run(EntityManager $em)
    {
        $em->persist($rootRole = (new Role())
            ->setName(Role::ROOT_NAME)
        );

        $em->persist($userRole = (new Role())
            ->setName(Role::USER_NAME)
        );

        $em->persist($moderatorRole = (new Role())
            ->setName(Role::MODERATOR_NAME)
        );

        $em->persist($user = (new User())
            ->setName('testing user1')
            ->setEmail('test1email@test.com')
            ->setPassword('secret')
            ->addRoles($userRole)
        );

        $em->persist($root = (new User())
            ->setName('testing user2')
            ->setEmail('test2email@gmail.com')
            ->setPassword('secret')
            ->addRoles($userRole)
            ->addRoles($rootRole)
        );

        $em->persist($moderator = (new User())
            ->setName('testing user3')
            ->setEmail('test3email@test.com')
            ->setPassword('secret')
            ->addRoles($userRole)
            ->addRoles($moderatorRole)
        );

        $em->persist($page = (new Page())
            ->setUser($user)
            ->setTitle('JSON:API standard')
            ->setContent('<strong>JSON:API</strong>')
        );

        $em->persist((new PageComment())
            ->setPage($page)
            ->setUser($user)
            ->setContent('<span>It is mine comment</span>')
        );

        $em->persist((new PageComment())
            ->setPage($page)
            ->setUser($root)
            ->setContent('<span>I know better</span>')
        );

        $em->persist((new PageComment())
            ->setPage($page)
            ->setUser($moderator)
            ->setContent('<span>I think he is right</span>')
        );

        $em->flush();
    }
}
