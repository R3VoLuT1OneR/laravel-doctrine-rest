<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelatedCollectionDeleteAction extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        ResourceRepository $repository,
        string $field,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->relatedResourceRepository = $related;
        $this->relatedFieldName = $field;
    }

    /**
     * @inheritdoc
     */
    public function handle(): Response
    {
        $resource = $this->repository()->findById($this->request->getId());
        $this->authorize($resource);

        foreach ($this->request->getData() as $raw) {
            $item = $this->findRelatedResource($raw);
            $this->manipulator()->removeRelationItem($resource, $this->relatedFieldName(), $item);
            $this->relatedResourceRepository()->em()->remove($item);
        }

        $this->repository()->em()->flush();

        return $this->response()->noContent();
    }
}
