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
            'limit'     => 'numeric',
            'page'      => 'numeric',
            'orderBy'   => 'string',
            'ascending' => 'boolean',
            'query'     => 'string',
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
        if ($start = $this->get('start')) {
            return (int) $start;
        }

        if ($page = $this->get('page')) {
            return $this->getLimit() * ((int) $page - 1);
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getDefaultLimit()
    {
        return 10;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->get('limit', $this->getDefaultLimit());
    }

    /**
     * @param string $alias
     *
     * @return OrderBy|null
     */
    public function getOrderBy()
    {
        if ($orderBy = $this->get('orderBy')) {
            if (is_string($orderBy)) {
                return [$orderBy => $this->get('ascending', true) ? 'ASC' : 'DESC'];
            }
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getQuery()
    {
        if ($query = $this->get('query')) {
            if (is_string($query) && (null !== ($json = json_decode($query, true)))) {
                return $json;
            }

            return $query;
        }

        return null;
    }
}
