<?php namespace Pz\LaravelDoctrine\Rest;

abstract class RestPolicy
{
    /**
     * @return bool
     */
    public function allowByDefault()
    {
        return false;
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function index(/** @scrutinizer ignore-unused */ $user)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function show(/** @scrutinizer ignore-unused */ $user)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     *
     * @return bool
     */
    public function create(/** @scrutinizer ignore-unused */ $user)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function update(/** @scrutinizer ignore-unused */ $user, /** @scrutinizer ignore-unused */ $entity)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function delete(/** @scrutinizer ignore-unused */ $user, $entity)
    {
        return $this->allowByDefault();
    }
}
