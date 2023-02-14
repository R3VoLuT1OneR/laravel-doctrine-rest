<?php namespace Pz\LaravelDoctrine\Rest\Traits;

use Illuminate\Contracts\Auth\Access\Gate;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\ResourceInterface;
use Pz\Doctrine\Rest\ResourceRepository;
use Pz\Doctrine\Rest\Response;

trait HandlesAuthorization
{
    abstract protected function restAbility(): string;

    abstract protected function repository(): ResourceRepository;

    public function gate(): Gate
    {
        return app(Gate::class);
    }

    public function authorize(?ResourceInterface $resource = null): void
    {
        if (!$this->allowed($resource)) {
            throw new RestException('This action is unauthorized.', Response::HTTP_FORBIDDEN);
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
