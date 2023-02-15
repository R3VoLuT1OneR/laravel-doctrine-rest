<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Doctrine\ORM\Query\QueryException;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListResource as BaseCollectionAction;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
* Action for providing collection (list or array) of data with API.
*/
class RelatedListResource extends BaseCollectionAction
{
    use RelatedActionTrait;

    public function __construct(
        ResourceRepository $repository,
        string $mappedBy,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->mappedBy = $mappedBy;
        $this->related = $related;
    }

    public function repository(): ResourceRepository
    {
        return $this->related;
    }

    public function base(): ResourceRepository
    {
        return $this->repository;
    }

    /**
     * Add filter by relation entity.
     * @throws RestException
     * @throws QueryException
     */
    protected function applyFilter(QueryBuilder $qb): static
    {
        $entity = $this->base()->findById($this->request->getId());

        $relateCriteria = Criteria::create();
        $relateCriteria->andWhere($relateCriteria->expr()->eq($this->mappedBy(), $entity->getId()));

        $qb->innerJoin($qb->getRootAliases()[0].'.'.$this->mappedBy(), $this->mappedBy());
        $qb->addCriteria($relateCriteria);

        return parent::applyFilter($qb);
    }
}
