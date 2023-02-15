<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Illuminate\Contracts\Auth\Access\Gate;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

/**
 * Used for verification access of the authenticated user to the resource.
 */
trait AuthorizeResource
{
    abstract public function resourceAccessAbility(): string;

    abstract public function repository(): ResourceRepository;

    public function gate(): Gate
    {
        return app(Gate::class);
    }

    public function authorize(?ResourceInterface $resource = null): void
    {
        if (!$this->allowed($resource)) {
            throw new RestException('This action is unauthorized.', JsonApiResponse::HTTP_FORBIDDEN);
        }
    }

    public function allowed(?ResourceInterface $resource = null): bool
    {
        return $this->gate()->allows($this->resourceAccessAbility(), [
            $resource ?: $this->repository()->getClassName()
        ]);
    }
}