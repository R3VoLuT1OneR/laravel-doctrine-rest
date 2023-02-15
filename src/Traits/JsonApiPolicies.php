<?php namespace Pz\LaravelDoctrine\JsonApi\Traits;

trait JsonApiPolicies
{
    public function defaultRestAccess($user, $resource = null): bool
    {
        return false;
    }

    public function restList($user): bool
    {
        return $this->defaultRestAccess($user);
    }

    public function restShow($user, $resource): bool
    {
        return $this->defaultRestAccess($user, $resource);
    }

    public function restCreate($user): bool
    {
        return $this->defaultRestAccess($user);
    }

    public function restUpdate($user, $resource): bool
    {
        return $this->defaultRestAccess($user, $resource);
    }

    public function restDelete($user, $resource): bool
    {
        return $this->defaultRestAccess($user, $resource);
    }
}
