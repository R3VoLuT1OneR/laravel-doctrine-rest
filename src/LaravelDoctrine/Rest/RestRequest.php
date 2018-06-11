<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Validation\ValidationException;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;

use Pz\Doctrine\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RestRequest extends FormRequest implements RestRequestContract
{
    /**
     * @var bool
     */
    protected $isRelationships = false;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'filter'        => 'sometimes|required',
            'include'       => 'sometimes|required',
            'exclude'       => 'sometimes|required|array',
            'fields'        => 'sometimes|required|array',
            'sort'          => 'sometimes|required|string',

            'page'          => 'sometimes|required|array',
            'page.number'   => 'sometimes|required|numeric',
            'page.size'     => 'sometimes|required|numeric',
            'page.limit'    => 'sometimes|required|numeric',
            'page.offset'   => 'sometimes|required|numeric',
        ];
    }

    /**
     * @param null|bool $value
     *
     * @return bool|null
     */
    public function isRelationships($value = null)
    {
        if ($value !== null) {
            $this->isRelationships = $value;
        }

        return $this->isRelationships;
    }

    /**
     * @return array
     * @throws RestException
     */
    public function getData()
    {
        if ((null === $data = $this->get('data')) || !is_array($data)) {
            throw RestException::missingRootData();
        }

        return $data;
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        if ($sort = $this->input('sort')) {
            $fields = explode(',', $sort);
            $orderBy = [];

            foreach ($fields as $field) {
                if (empty($field)) continue;

                $direction = 'ASC';
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = 'DESC';
                }

                $orderBy[$field] = $direction;
            }

            return $orderBy;
        }

        return null;
    }

    /**
     * @return null|int
     */
    public function getStart()
    {
        if (null !== ($limit = $this->getLimit())) {
            if ($number = $this->input('page.number')) {
                return ($number - 1) * $limit;
            }

            return $this->input('page.offset', 0);
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        if ($this->has('page')) {
            if ($this->has('page.number')) {
                return $this->input('page.size', static::DEFAULT_LIMIT);
            }

            return $this->input('page.limit', static::DEFAULT_LIMIT);
        }

        return null;
    }

    /**
     * @return \Illuminate\Routing\Route|object|string
     */
    public function getId()
    {
        return $this->route('id');
    }

    /**
     * @return array|null
     */
    public function getExclude()
    {
        return $this->input('exclude');
    }

    /**
     * @return array|null
     */
    public function getInclude()
    {
        $include = @explode(',', $this->input('include'));

        if (!is_array($include)) {
            RestException::invalidInclude();
        }

        return $include;
    }

    public function getFields()
    {
        return $this->input('fields');
    }

    /**
     * @return array|string|null
     */
    public function getFilter()
    {
        return $this->input('filter');
    }

    /**
     * @return bool
     */
    protected function passesAuthorization()
    {
        return true;
    }

    /**
     * @param Validator $validator
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $exception = RestException::create(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation failed');
        foreach ($validator->errors()->getMessages() as $pointer => $messages) {
            foreach ($messages as $message) {
                $exception->errorValidation($pointer, $message);
            }
        }

        throw new ValidationException($validator, RestResponse::exception($exception));
    }
}
