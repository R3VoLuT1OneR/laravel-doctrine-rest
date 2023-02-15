<?php namespace Tests\App\Providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;
use Tests\App\Entities\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register base service provider
     */
    public function register()
    {
        $this->allowRootPermissions();
    }

    /**
     * Allow all permissions for users with `root` role.
     */
    protected function allowRootPermissions()
    {
        /** @var Gate $gate */
        $gate = $this->app->make(Gate::class);
        $gate->before(function($user) {
            if ($user instanceof User && $user->isRoot()) {
                return true;
            }

            return null;
        });
    }

}
