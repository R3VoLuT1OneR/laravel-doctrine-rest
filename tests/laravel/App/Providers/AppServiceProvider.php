<?php namespace Tests\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Policies\PageCommentPolicy;
use Tests\App\Policies\PagePolicy;
use Tests\App\Policies\RolePolicy;
use Tests\App\Policies\UserPolicy;

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
        Gate::guessPolicyNamesUsing(fn () => false);

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(PageComment::class, PageCommentPolicy::class);

        Gate::before(function($user) {
            if ($user instanceof User && $user->isRoot()) {
                return true;
            }

            return null;
        });
    }
}
