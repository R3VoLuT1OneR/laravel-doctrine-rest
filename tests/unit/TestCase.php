<?php namespace Tests;

use Database\Seeders\SetUpSeeder;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Tests\App\Entities\User;
use Tests\App\Policies\UserPolicy;
use Tests\Helpers\WithEntityManager;

class TestCase extends LaravelTestCase
{
    use WithEntityManager;

    protected Kernel $kernel;

    /**
     * @return Application
     */
    public function createApplication()
    {
        /** @var Application $app */
        $app = require realpath(__DIR__) . '/../laravel/bootstrap/app.php';

        $this->kernel = $app->make(Kernel::class);
        $this->kernel->bootstrap();

        $app->register(DoctrineServiceProvider::class);
        $app->register(MigrationsServiceProvider::class);

        $this->kernel->call('doctrine:migrations:refresh');

        $this->em = $app->make(EntityManager::class);

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SetUpSeeder::class);
        $this->em()->clear();
    }

    public function seed($class = 'Database\\Seeders\\DatabaseSeeder')
    {
        if (!class_exists($class)) {
            throw new \Exception(sprintf("Seeder not found: %s", $class));
        }

        if (!method_exists($class, 'run')) {
            throw new \Exception(sprintf("Seeder missing '%s::run' method.", $class));
        }

        $this->em()->clear();
        $seeder = new $class;
        $seeder->run($this->em);
        $this->em()->clear();

        return $this;
    }

    protected function actingAsRoot(): User
    {
        $this->actingAs($user = $this->em->find(User::class, 2));
        return $user;
    }

    protected function actingAsUser(): User
    {
        $this->actingAs($user = $this->em->find(User::class, 1));
        return $user;
    }

    protected function actingAsModerator(): User
    {
        $this->actingAs($user = $this->em->find(User::class, 3));
        return $user;
    }
}
