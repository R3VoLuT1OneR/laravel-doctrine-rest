<?php namespace Pz\LaravelDoctrine\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Auth\Access\AuthorizationException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\ForbiddenException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;

/**
 * Any JSON:API endpoint handler should inherit this class.
 * It has all the dependencies required for any action to be implemented.
 *
 * Such as doctrine repository, transformer, manipulator.
 *
 * The dispatch method must be called with provided request.
 * Dispatch method will call the handle method injecting its dependencies and will return JsonApiResponse.
 *
 * @method JsonApiResponse handle(...$args) The handle method must be implemented, but we do not define it as abstract
 *                                          because we PHP do not allow arguments override , and we want to use laravel
 *                                          dependency injection container to get dependencies.
 */
abstract class AbstractAction
{
    protected JsonApiRequest $request;

    public function __construct(
        protected ResourceRepository $repository,
        protected AbstractTransformer $transformer,
        protected ?ResourceManipulator $manipulator = null
    ) {
        $this->manipulator = $this->manipulator ?? new ResourceManipulator($this->repository->em());
    }

    public function dispatch(JsonApiRequest $request): JsonApiResponse
    {
        $this->request = $request;

        try {
            return app()->call([$this, 'handle']);
        } catch (AuthorizationException $e) {
            return response()->exception(new ForbiddenException(
                previous: $e
            ));
        } catch (RestException $e) {
            return response()->exception($e);
        }
    }

    protected function repository(): ResourceRepository
    {
        return $this->repository;
    }

    protected function transformer(): AbstractTransformer
    {
        return $this->transformer;
    }

    protected function request(): JsonApiRequest
    {
        return $this->request;
    }

    protected function manipulator(): ResourceManipulator
    {
        return $this->manipulator;
    }

    protected function em(): EntityManager
    {
        return $this->repository()->em();
    }
}
