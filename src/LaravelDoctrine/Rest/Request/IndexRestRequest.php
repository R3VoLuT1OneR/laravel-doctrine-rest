<?php namespace Pz\LaravelDoctrine\Rest\Request;

use Illuminate\Validation\Factory;
use Doctrine\ORM\Query\Expr\OrderBy;

use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;

class IndexRestRequest extends RestRequest implements IndexRequestInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            // Query
            'filter'        => 'string',

            // Pagination
            'page'          => 'array',
            'page.offset'   => 'required_with:page|numeric',
            'page.limit'    => 'required_with:page|numeric',

            // Sorting
            'sort'          => 'string',
        ];
    }

    /**
     * @return string
     */
    public function ability()
    {
         return 'index';
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->input('page.offset', 0);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->input('page.limit', 50);
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        if ($fields = explode(',', $this->get('sort'))) {
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
     * @return mixed|null
     */
    public function getQuery()
    {
        if ($query = $this->get('filter')) {
            if (is_string($query) && (null !== ($json = json_decode($query, true)))) {
                return $json;
            }

            return $query;
        }

        return null;
    }
}
