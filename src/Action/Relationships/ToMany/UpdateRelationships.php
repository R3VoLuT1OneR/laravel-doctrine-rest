<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Doctrine\Common\Collections\Collection;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelationshipsTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;
    use RelationshipsActionTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string $relatedFieldName,
        protected string $resourceMappedBy,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        $replaceRelationships = new ArrayCollection(array_map(
            function (array $relatedPrimaryData, $index) {
                return $this
                    ->relatedResourceRepository()
                    ->findByPrimaryData($relatedPrimaryData, "/data/$index");
            },
            $this->request()->getData(),
            array_keys($this->request()->getData()),
        ));

        $this->manipulator()->replaceResourceCollection($resource, $this->relatedFieldName(), $replaceRelationships);
        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::UPDATE_RELATIONSHIPS;
    }
}
