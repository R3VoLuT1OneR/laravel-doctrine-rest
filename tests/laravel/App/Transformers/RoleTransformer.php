<?php namespace Tests\App\Transformers;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;

class RoleTransformer extends AbstractTransformer
{
    public function transform(Role $role): array
    {
        return [
            'id' => $role->getId(),
            'name' => $role->getName(),
        ];
    }
}
