<?php

namespace Database\Seeders;

use Doctrine\ORM\EntityManager;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class SetUpSeeder
{
    public function run(EntityManager $em)
    {
        $em->persist($root = (new Role())
            ->setName(Role::ROOT_NAME)
        );

        $em->persist($user = (new Role())
            ->setName(Role::USER_NAME)
        );

        $em->persist($moderator = (new Role())
            ->setName(Role::MODERATOR_NAME)
        );

        $em->persist((new User())
            ->setName('testing user1')
            ->setEmail('test1email@test.com')
            ->setPassword('secret')
            ->addRoles($user)
        );

        $em->persist((new User())
            ->setName('testing user2')
            ->setEmail('test2email@gmail.com')
            ->setPassword('secret')
            ->addRoles($root)
        );

        $em->persist((new User())
            ->setName('testing user3')
            ->setEmail('test3email@test.com')
            ->setPassword('secret')
            ->addRoles($moderator)
        );

        $em->flush();
    }
}
