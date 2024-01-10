<?php namespace Pz\LaravelDoctrine\Rest\Tests;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use \Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;

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

        $this->kernel->call('doctrine:migrations:refresh', [
            '--no-interaction' => true,
        ]);

        $this->em = $app->make(EntityManager::class);

        /** @var Gate $gate */
        $gate = $app->make(Gate::class);
        $gate->policy(User::class, UserPolicy::class);

        return $app;
    }
}
