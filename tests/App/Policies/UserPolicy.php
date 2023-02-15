<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Policies;

use Pz\LaravelDoctrine\JsonApi\Traits\JsonApiPolicies;

class UserPolicy
{
    use JsonApiPolicies;

    public function restRelationshipsCollectionCreate($user, $resource, $class)
    {
        return false;
    }

    public function restRelationshipsCollectionUpdate($user, $resource, $class)
    {
        $test = false;
        return false;
    }
}
