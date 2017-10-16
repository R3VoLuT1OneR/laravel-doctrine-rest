<?php namespace Pz\LaravelDoctrine\Rest;

class RestPolicy
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
     * @param string $entityClass
     *
     * @return bool
     */
    public function index($user)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function show($user, $entity)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param string $entityClass
     *
     * @return bool
     */
    public function create($user)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function update($user, $entity)
    {
        return $this->allowByDefault();
    }

    /**
     * @param object $user
     * @param object $entity
     *
     * @return bool
     */
    public function delete($user, $entity)
    {
        return $this->allowByDefault();
    }
}
