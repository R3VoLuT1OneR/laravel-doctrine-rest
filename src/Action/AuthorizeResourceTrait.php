<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Illuminate\Contracts\Auth\Access\Gate;
use Pz\LaravelDoctrine\JsonApi\Exceptions\ForbiddenException;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
 * Used for verification access of the authenticated user to the resource.
 */
trait AuthorizeResourceTrait
{
    /**
     * Ability to check for access to the requested resource in the root level.
     */
    abstract public function resourceAccessAbility(): string;

    abstract public function repository(): ResourceRepository;

    /**
     * @throws ForbiddenException
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
        $ability = $this->resourceAccessAbility();
        $allowed = $this->gate()->allows($ability, [
            $resource ?: $this->repository()->getClassName()
        ]);

        if (!$allowed) {
            throw ForbiddenException::create()
                ->errorAtPointer('/', sprintf(
                    'No "%s" ability on "%s" resource.', $ability,
                    $this->repository()->getResourceKey()
                ));
        }
    }

    protected function gate(): Gate
    {
        return app(Gate::class);
    }
}
