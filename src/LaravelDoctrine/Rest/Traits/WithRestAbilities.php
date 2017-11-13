<?php namespace Pz\LaravelDoctrine\Rest\Traits;

trait WithRestAbilities
{
    /**
     * @return bool
     */
    public function defaultRestAccess(/** @scrutinizer ignore-unused */$user)
    {
        return false;
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function restIndex($user)
    {
        return $this->defaultRestAccess($user);
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function restShow($user, /** @scrutinizer ignore-unused */ $entity)
    {
        return $this->defaultRestAccess($user);
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function restCreate($user)
    {
        return $this->defaultRestAccess($user);
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function restUpdate($user, /** @scrutinizer ignore-unused */ $entity)
    {
        return $this->defaultRestAccess($user);
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function restDelete($user,  /** @scrutinizer ignore-unused */$entity)
    {
        return $this->defaultRestAccess($user);
    }
}
