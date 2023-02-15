<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListRelatedResources;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;

use Doctrine\Common\Collections\ArrayCollection;

class RelationshipsCollectionUpdateAction extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeResource;

    public function __construct(
        ResourceRepository $repository,
        string $field,
        string $mappedBy,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->resourceMappedBy = $mappedBy;
        $this->relatedResourceRepository = $related;
        $this->relatedFieldName = $field;
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request->getId());

        $this->authorize($resource);

        $items = $this->manipulator()->getProperty($resource, $this->relatedFieldName());

        $replace = new ArrayCollection(array_map(
            function($raw) use ($resource) {
                return $this->findRelatedResource($raw);
            },
            $this->request->getData()
        ));

        foreach ($items as $key => $item) {
            if (!$replace->contains($item)) {
                $items->remove($key);
            }
        }

        foreach ($replace as $item) {
            if (!$items->contains($item)) {
                $items->add($item);
            }
        }

        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $items);

        $this->repository()->em()->flush($resource);

        return (
            new ListRelatedResources(
                $this->repository(),
                $this->resourceMappedBy(),
                $this->relatedResourceRepository(),
                $this->transformer()
            )
        )->dispatch($this->request);
    }

    protected function resourceAccessAbility(): string
    {
        return 'restRelationshipsCollectionUpdate';
    }

    public function allowed(?ResourceInterface $resource = null): bool
    {
        $ability = $this->resourceAccessAbility();
        $arguments = [$resource, $this->relatedResourceRepository()->getClassName()];
        $allowed = $this->gate()->allows($ability, $arguments);
        return $allowed;
    }
}
