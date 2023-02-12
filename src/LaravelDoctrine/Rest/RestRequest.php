<?php namespace Pz\LaravelDoctrine\Rest;

use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RequestInterface;
use Pz\Doctrine\Rest\RestResponse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

use Symfony\Component\HttpFoundation\Response;

class RestRequest extends FormRequest implements RequestInterface
{
    protected bool $isRelationships = false;

    public function rules(): array
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

    public function isRelationships(bool $value = null): bool
    {
        if ($value !== null) {
            $this->isRelationships = $value;
        }

        return $this->isRelationships;
    }

    public function getBaseUrl(): string
    {
        return parent::getBaseUrl();
    }

    /**
     * @throws RestException
     */
    public function getData(): ?array
    {
        if ((null === $data = $this->get('data')) || !is_array($data)) {
            throw RestException::missingRootData();
        }

        return $data;
    }

    public function getOrderBy(): ?array
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

    public function getStart(): ?int
    {
        if (null !== ($limit = $this->getLimit())) {
            if ($number = $this->input('page.number')) {
                return ($number - 1) * $limit;
            }

            return $this->input('page.offset', 0);
        }

        return null;
    }

    public function getLimit(): ?int
    {
        if ($this->has('page')) {
            if ($this->has('page.number')) {
                return $this->input('page.size', static::DEFAULT_LIMIT);
            }

            return $this->input('page.limit', static::DEFAULT_LIMIT);
        }

        return null;
    }

    public function getId(): string
    {
        return $this->route('id');
    }

    public function getExclude(): ?array
    {
        return $this->input('exclude');
    }

    public function getInclude(): ?array
    {
        $include = @explode(',', $this->input('include'));

        if (!is_array($include)) {
            RestException::invalidInclude();
        }

        return $include;
    }

    public function getFields(): ?array
    {
        return $this->input('fields');
    }

    public function getFilter(): mixed
    {
        return $this->input('filter');
    }

    protected function passesAuthorization(): bool
    {
        return true;
    }

    /**
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
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
