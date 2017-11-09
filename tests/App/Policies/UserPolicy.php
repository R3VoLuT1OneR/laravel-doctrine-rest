<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Policies;

use Pz\LaravelDoctrine\Rest\RestPolicy;

class UserPolicy extends RestPolicy
{
    /**
     * @param object $user
     *
     * @return bool
     */
    public function index($user)
    {
        return parent::default() || parent::index($user);
    }

    /**
     * @return bool
     */
    public function default()
    {
        return true;
    }
}
