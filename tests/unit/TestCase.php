<?php namespace Tests;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Tests\App\Entities\User;
use Tests\App\Policies\UserPolicy;

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
        $app = require realpath(__DIR__) . '/../dummy/bootstrap/app.php';

        $this->kernel = $app->make(Kernel::class);
        $this->kernel->bootstrap();

        $app->register(DoctrineServiceProvider::class);
        $app->register(MigrationsServiceProvider::class);

        $this->kernel->call('doctrine:migrations:refresh');

        $this->em = $app->make(EntityManager::class);

        /** @var Gate $gate */
        $gate = $app->make(Gate::class);
        $gate->policy(User::class, UserPolicy::class);

        return $app;
    }

    protected function actingAsUser(): User
    {
        $this->actingAs($user = $this->em->find(User::class, 1));

        return $user;
    }

    protected function actingAsRoot(): User
    {
        $this->actingAs($user = $this->em->find(User::class, 2));

        return $user;
    }
}
