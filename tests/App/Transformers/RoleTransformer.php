<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Transformers;

use League\Fractal\TransformerAbstract;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;

class RoleTransformer extends TransformerAbstract
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
