<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Transformers;

use League\Fractal\TransformerAbstract;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserTransformer extends TransformerAbstract
{
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
}
