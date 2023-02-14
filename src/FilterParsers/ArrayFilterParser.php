<?php namespace Pz\LaravelDoctrine\JsonApi\FilterParsers;

use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Exceptions\BadRequestException;
use Doctrine\Common\Collections\Criteria;

class ArrayFilterParser extends AbstractFilterParser
{
    public function __construct(JsonApiRequest $request, protected array $filterable)
    {
        parent::__construct($request);
    }

    public function applyFilter(Criteria $criteria): Criteria
    {
        $filter = $this->request->getFilter();

        if (!is_array($filter)) {
            return $criteria;
        }

        foreach ($this->filterable as $field) {
            if (array_key_exists($field, $filter)) {
                $this->processEqualFilter($criteria, $field, $filter[$field]);
                $this->processBetweenFilter($criteria, $field, $filter[$field]);
                $this->processOperatorFilter($criteria, $field, $filter[$field]);
            }
        }

        return $criteria;
    }

    protected function processEqualFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_string($value)) {
            $criteria->andWhere(
                $criteria->expr()->eq($field, $value)
            );
        }

        return $this;
    }

    protected function processBetweenFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_array($value) && isset($value['start']) && isset($value['end'])) {
            $criteria->andWhere($criteria->expr()->andX(
                $criteria->expr()->gte($field, $value['start']),
                $criteria->expr()->lte($field, $value['end'])
            ));
        }

        return $this;
    }

    protected function processOperatorFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_array($value) && isset($value['operator']) && array_key_exists('value', $value)) {
            $operator = $value['operator'];

            if (!method_exists($criteria->expr(), $operator)) {
                throw (new BadRequestException('Unknown filter operator.'))
                    ->error('filter-array-unknown-operator', ['field' => $field, 'filter' => $value], 'Unknown operator');
            }

            $criteria->andWhere(
                $criteria->expr()->$operator($field, $value['value'])
            );
        }

        return $this;
    }
}
