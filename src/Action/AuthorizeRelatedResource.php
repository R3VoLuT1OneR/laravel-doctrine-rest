<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Illuminate\Auth\Access\AuthorizationException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\ForbiddenException;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
 * Used for access verification to resource and related resources.
 * It's verifies that user have access to resource and related resource.
 */
trait AuthorizeRelatedResource
{
    use AuthorizeResource {
        authorize as authorizeResource;
    }

    abstract public function relatedResourceRepository(): ResourceRepository;

    abstract public function relatedResourceAccessAbility(): string;

    /**
     * @throws ForbiddenException
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
        $this->authorizeResource($resource);

        $ability = $this->relatedResourceAccessAbility();
        $allowed = $this->gate()->allows($ability, [
            $resource,
            $this->relatedResourceRepository()->getClassName()
        ]);

        if (!$allowed) {
            throw ForbiddenException::create()
                ->errorAtPointer('/', sprintf(
                    'No "%s" ability on "%s" and related "%s" resource.', $ability,
                    $this->repository()->getResourceKey(),
                    $this->relatedResourceRepository()->getResourceKey(),
                ));
        }
    }
}
