<?php namespace Pz\LaravelDoctrine\Rest\Traits;

use Illuminate\Contracts\Auth\Access\Gate;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\Resource\ResourceInterface;
use Pz\Doctrine\Rest\RestRepository;

trait HandlesAuthorization
{
    abstract protected function restAbility(): string;

    abstract protected function repository(): RestRepository;

    public function gate(): Gate
    {
        return app(Gate::class);
    }

    /**
     * @throws RestException
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
        if (!$this->allowed($resource)) {
            throw RestException::createForbidden('This action is unauthorized.');
        }
    }

    public function allowed(?ResourceInterface $resource = null): bool
    {
        return $this->gate()->allows(
            $this->restAbility(),
            $resource ?: $this->repository()->getClassName()
        );
    }
}
