<?php namespace Tests\App\Transformers;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;

class RoleTransformer extends AbstractTransformer
{
    /**
     * @param Role $role
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => $role->getId(),
            'name' => $role->getName(),
        ];
    }
}
