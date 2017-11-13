<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Policies;

use Pz\LaravelDoctrine\Rest\Traits\WithRestAbilities;

class UserPolicy
{
    use WithRestAbilities {
        defaultRestAccess as parentRestAccess;
        restIndex as parentRestIndex;
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function restIndex($user)
    {
        return $this->parentRestAccess($user) || $this->parentRestIndex($user);
    }

    /**
     * @return bool
     */
    public function defaultRestAccess()
    {
        return true;
    }
}
