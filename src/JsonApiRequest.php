<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Doctrine\Common\Collections\Criteria;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;

/**
 * TODO: Compare with last version of PZ/DoctrinRest and add improved stuff here.
 */
class JsonApiRequest extends FormRequest
{
    const KEY_DATA = 'data';

    const QUERY_KEY_FIELDS = 'fields';
    const QUERY_KEY_FILTER = 'filter';
    const QUERY_KEY_SORT = 'sort';
    const QUERY_KEY_PAGE = 'page';
    const QUERY_KEY_PAGE_NUMBER = 'number';
    const QUERY_KEY_PAGE_OFFSET = 'offset';
    const QUERY_KEY_PAGE_SIZE = 'size';
    const QUERY_KEY_PAGE_LIMIT = 'limit';
    const QUERY_KEY_INCLUDE = 'include';
    const QUERY_KEY_EXCLUDE = 'exclude';

    /**
     * Json API type.
     */
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Default limit for list.
     */
    const DEFAULT_LIMIT = 1000;

    public function rules(): array
    {
        return $this->dataRules() + $this->queryParamsRules();
    }

    public function dataRules(): array
    {
        return [];
    }

    public function queryParamsRules(): array
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

    public function getBaseUrl(): string
    {
        return parent::getBaseUrl();
    }

    public function getData(): ?array
    {
        $validated = $this->validated();

        if (!isset($validated['data'])) {
            throw RestException::create('Not valid data', 400)
                ->error(400, ['pointer' => '/data'], 'Not found any validated data.');
        }

        return $validated['data'];
    }

    public function getSort(): array
    {
        $sortBy = [];

        $sort = $this->get(static::QUERY_KEY_SORT);
        if (is_string($sort)) {
            $fields = explode(',', $sort);

            foreach ($fields as $field) {
                $direction = Criteria::ASC;
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = Criteria::DESC;
                }

                $sortBy[$field] = $direction;
            }
        }

        return $sortBy;
    }

    public function getPage(): array|null
    {
        $page = $this->get(static::QUERY_KEY_PAGE);
        if (is_array($page)) {
            return $page;
        }

        return null;
    }

    public function getFirstResult(): int|null
    {
        $page = $this->getPage();
        $maxResults = $this->getMaxResults();

        if (is_array($page) && !is_null($maxResults)) {
            if (isset($page[static::QUERY_KEY_PAGE_NUMBER]) && is_numeric($page[static::QUERY_KEY_PAGE_NUMBER])) {
                return ((int) $page[static::QUERY_KEY_PAGE_NUMBER] - 1) * $maxResults;
            }

            if (isset($page[static::QUERY_KEY_PAGE_OFFSET]) && is_numeric($page[static::QUERY_KEY_PAGE_OFFSET])) {
                return (int) $page[static::QUERY_KEY_PAGE_OFFSET];
            }

            return 0;
        }

        return null;
    }

    public function getMaxResults(): int|null
    {
        $page = $this->getPage();

        if (is_array($page)) {
            if (isset($page[static::QUERY_KEY_PAGE_NUMBER]) && is_numeric($page[static::QUERY_KEY_PAGE_NUMBER])) {
                if (isset($page[static::QUERY_KEY_PAGE_SIZE]) && is_numeric($page[static::QUERY_KEY_PAGE_SIZE])) {
                    return (int) $page[static::QUERY_KEY_PAGE_SIZE];
                }
            }

            if (isset($page[static::QUERY_KEY_PAGE_LIMIT]) && is_numeric($page[static::QUERY_KEY_PAGE_LIMIT])) {
                return (int) $page[static::QUERY_KEY_PAGE_LIMIT];
            }

            return static::DEFAULT_LIMIT;
        }

        return null;
    }

    public function getId(): string
    {
        return $this->route('id');
    }
    public function getInclude(): array
    {
        $include = $this->get(static::QUERY_KEY_INCLUDE);

        if (is_string($include)) {
            return explode(',', $include);
        }

        return [];
    }

    public function getExclude(): array
    {
        $exclude = $this->get(static::QUERY_KEY_EXCLUDE);

        if (is_string($exclude)) {
            return explode(',', $exclude);
        }

        return [];
    }

    public function getFields(): array
    {
        $fields = $this->get(static::QUERY_KEY_FIELDS);

        if (is_array($fields)) {
            return $fields;
        }

        return [];
    }

    public function getFilter(): mixed
    {
        $filter = $this->get(static::QUERY_KEY_FILTER);

        if (is_string($filter)) {
            // Try to decode the string value as JSON.
            // Allow passing "filter" value as JSON encoded string.
            $json = json_decode($filter, true);
            if (is_string($json) || is_array($json)) {
                return $json;
            }

            return $filter;
        }

        if (is_array($filter)) {
            return $filter;
        }

        return null;
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
        $exception = new Exceptions\ValidationException();
        foreach ($validator->errors()->getMessages() as $attribute => $messages) {
            foreach ($messages as $message) {
                $pointer = "/".str_replace('.', '/', $attribute);
                $exception->validationError($pointer, $message);
            }
        }

        throw new ValidationException(
            $validator,
            new JsonApiResponse(['errors' => $exception->errors()], $exception->getCode())
        );
    }
}
