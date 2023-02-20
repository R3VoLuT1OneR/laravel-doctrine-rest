<?php namespace Pz\LaravelDoctrine\JsonApi\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class SearchFilterParser extends AbstractFilterParser
{
    const SEARCH_KEY = 'search';

    public function __construct(
        JsonApiRequest $request,
        protected ?string $property,
        protected string $searchKey = self::SEARCH_KEY
    ) {
        parent::__construct($request);
    }

    /**
     * Assign LIKE operator on property if query is string.
     */
    public function applyFilter(Criteria $criteria): Criteria
    {
        $filter = $this->request->getFilter();

        if (is_string($filter) && is_string($this->property)) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter)
            );
        }

        if (is_array($filter) && isset($filter[$this->searchKey])) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter[$this->searchKey])
            );
        }

        return $criteria;
    }
}
