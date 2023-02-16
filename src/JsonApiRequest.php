<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataException;

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
        if ((null === $data = $this->get('data')) || !is_array($data)) {
            throw new MissingDataException("/");
        }

        return $data;
    }

    public function getSort(): array
    {
        $sortBy = [];

        if ($sort = $this->input('sort')) {
            $fields = explode(',', $sort);

            foreach ($fields as $field) {
                if (empty($field)) continue;

                $direction = 'ASC';
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = 'DESC';
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

    public function getExclude(): array
    {
        return $this->input(static::QUERY_KEY_EXCLUDE, []);
    }

    public function getInclude(): array
    {
        return @explode(',', $this->input(static::QUERY_KEY_INCLUDE)) ?: [];
    }

    public function getFields(): array
    {
        return $this->input('fields', []);
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
        $exception = new Exceptions\ValidationException();
        foreach ($validator->errors()->getMessages() as $pointer => $messages) {
            foreach ($messages as $message) {
                $exception->validationError($pointer, $message);
            }
        }

        throw new ValidationException(
            $validator,
            new JsonApiResponse(['errors' => $exception->errors()], $exception->getCode())
        );
    }
}
