<?php namespace Pz\LaravelDoctrine\Rest\Tests;

use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use \Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;

use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;
use Pz\LaravelDoctrine\Rest\Tests\App\Policies\UserPolicy;

class TestCase extends LaravelTestCase
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @return Application
     */
    public function createApplication()
    {
        /** @var Application $app */
        $app = require __DIR__.'/bootstrap/app.php';

        $this->kernel = $app->make(Kernel::class);
        $this->kernel->bootstrap();

        $app->register(DoctrineServiceProvider::class);
        $app->register(MigrationsServiceProvider::class);

        $this->kernel->call('doctrine:migrations:migrate', [
            '--no-interaction' => true,
        ]);

        $this->em = $app->make(EntityManager::class);

        $this->seedUsersAndRoles($this->em);

        /** @var Gate $gate */
        $gate = $app->make(Gate::class);
        $gate->policy(User::class, UserPolicy::class);

        return $app;
    }

    private function seedUsersAndRoles(EntityManager $em): void
    {
        $secret = bcrypt('secret');

        $em->persist(
            $root = (new Role())
                ->setName(Role::ROOT_NAME)
        );

        $em->persist(
            $userRole = (new Role())
                ->setName(Role::USER_NAME)
        );

        $em->persist(
            (new User())
                ->setName('testing user1')
                ->setEmail('test1email@test.com')
                ->setPassword($secret)
                ->addRoles($root)
        );

        $em->persist(
            (new User())
                ->setName('testing user2')
                ->setEmail('test2email@gmail.com')
                ->setPassword($secret)
                ->addRoles($userRole)
        );

        $em->persist(
            (new User())
                ->setName('testing user3')
                ->setEmail('test3email@test.com')
                ->setPassword($secret)
                ->addRoles($userRole)
        );

        $em->flush();
        $em->clear();
    }
}
