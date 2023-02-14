<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Transformers;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserTransformer extends AbstractTransformer
{
    /**
     * @var array
     */
    protected $availableIncludes = [
        'roles'
    ];

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }

    /**
     * @param User $user
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeRoles(User $user)
    {
        return $this->collection($user->getRoles(), new RoleTransformer(), Role::getResourceKey());
    }
}
