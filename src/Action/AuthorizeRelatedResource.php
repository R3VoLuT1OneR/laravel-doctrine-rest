<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * Used for access verification to resource and related resources.
 * It's verifies that user have access to resource and related resource.
 */
trait AuthorizeRelatedResource
{
    use AuthorizeResource {
        allowed as resourceAllowed;
    }

    abstract public function relatedResourceAccessAbility(): string;

    public function allowed(ResourceInterface $resource): bool
    {
        if (!$this->resourceAllowed($resource)) {
            return false;
        }

        return $this->gate()->allows($this->relatedResourceAccessAbility(), [
            $resource,
        ]);
    }
}