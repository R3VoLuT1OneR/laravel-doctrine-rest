<?php namespace Pz\LaravelDoctrine\Rest;

abstract class RestPolicy
{
    /**
     * @return bool
     */
    public function defaultAccess(/** @scrutinizer ignore-unused */ $user)
    {
        return false;
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function index($user)
    {
        return $this->defaultAccess($user);
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function show($user, /** @scrutinizer ignore-unused */ $entity)
    {
        return $this->defaultAccess($user);
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function create($user)
    {
        return $this->defaultAccess($user);
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function update($user, /** @scrutinizer ignore-unused */ $entity)
    {
        return $this->defaultAccess($user);
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function delete($user, $entity)
    {
        return $this->defaultAccess($user);
    }
}
