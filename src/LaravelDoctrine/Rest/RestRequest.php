<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Foundation\Http\FormRequest;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class RestRequest extends FormRequest implements RestRequestContract
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'filter'        => 'sometimes|required',
            'include'       => 'sometimes|required|array',
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
     * @return bool
     */
    public function isAcceptJsonApi()
    {
        return in_array(RestRequestContract::JSON_API_CONTENT_TYPE, $this->getAcceptableContentTypes());
    }

    /**
     * @return bool
     */
    public function isContentJsonApi()
    {
        return $this->headers->get('CONTENT_TYPE') === static::JSON_API_CONTENT_TYPE;
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
        return $this->input('include');
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
}
