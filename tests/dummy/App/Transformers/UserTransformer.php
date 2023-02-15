<?php namespace Tests\App\Transformers;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserTransformer extends AbstractTransformer
{
    /**
     * @var array
     */
    protected array $availableIncludes = [
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
