<?php namespace Pz\LaravelDoctrine\Rest\Traits;

use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Contracts\Auth\Access\Gate;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRequest;

use Symfony\Component\HttpFoundation\Response;

trait HandlesAuthorization
{
    /**
     * Action ability.
     *
     * @return string
     */
    abstract protected function restAbility();

    /**
     * @param RestRequest   $request
     * @param array|mixed   $arguments
     * @throws RestException
     */
    public function authorize($request, $arguments = [])
    {
        try {

            /** @var Gate $gate */
            $gate = app(Gate::class);
            $gate->authorize($this->restAbility(), $arguments);

        } catch (AuthorizationException $e) {
            throw RestException::createForbidden($e->getMessage());
        }
    }
}
