<?php namespace Pz\LaravelDoctrine\JsonApi\Action\List\FilterParsers;

use Pz\LaravelDoctrine\JsonApi\Action\List\FilterParsers\BuilderChain\MemberInterface;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Doctrine\Common\Collections\Criteria;

abstract class AbstractFilterParser implements MemberInterface
{
    abstract public function applyFilter(Criteria $criteria): Criteria;

    public function __construct(protected JsonApiRequest $request) {}

    public function __invoke($object): Criteria
    {
        return $this->applyFilter($object);
    }
}