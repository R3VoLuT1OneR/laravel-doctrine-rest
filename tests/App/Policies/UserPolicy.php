<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Policies;

use Pz\LaravelDoctrine\JsonApi\Traits\WithRestAbilities;

class UserPolicy
{
    use WithRestAbilities;

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
