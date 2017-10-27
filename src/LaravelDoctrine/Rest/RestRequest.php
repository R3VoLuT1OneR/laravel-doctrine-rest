<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\ValidationException;
use Pz\Doctrine\Rest\RestRequestInterface;

abstract class RestRequest extends FormRequest implements RestRequestInterface
{
    /**
     * Authorization gateway ability.
     * Return `false` for pass auth.
     *
     * @return null|string
     */
    abstract public function ability();

    /**
     * @return $this
     */
    public function http()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @param object|string $entity
     * @throws AuthorizationException
     * @return void
     */
    public function authorize($entity)
    {
        $this->gate()->authorize($this->ability(), $entity);
    }

    /**
     * @param Validator $validator
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))->errorBag($this->errorBag);
    }

    /**
     * Laravel authorization gate.
     *
     * @return Gate
     */
    public function gate()
    {
        return $this->container->make(Gate::class);
    }

    /**
     * Pass default request authorization.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        return true;
    }
}
