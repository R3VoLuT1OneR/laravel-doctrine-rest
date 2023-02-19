<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Exceptions\BadRequestException;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait RelatedActionTrait
{
    protected ResourceRepository $relatedResourceRepository;
    protected string $relatedFieldName;
    protected string $resourceMappedBy;

    abstract public function repository(): ResourceRepository;
    abstract public function request(): JsonApiRequest;

    /**
     * Repository of the related resource.
     * Can be used for fetching related resources.
     */
    public function relatedResourceRepository(): ResourceRepository
    {
        return $this->relatedResourceRepository;
    }

    /**
     * The field name that wanted related entity is saved on the resource.
     *
     * For example if we work with "author" field on "comment" resource, we will provide "author" for related
     * resources' manipulation.
     *
     * Then if we need to show relation getter "getAuthor" will be called and in case we need ot set then setter
     * "setAuthor" will be used.
     */
    public function relatedFieldName(): string
    {
        return $this->relatedFieldName;
    }

    /**
     * The resource is mapped by this field in doctrine relation (reverse relation).
     * Basically the "mappedBy" field on the related entity.
     */
    public function resourceMappedBy(): string
    {
        return $this->resourceMappedBy;
    }

    /**
     * Find resource interface by the primary data.
     *
     * Example: [
     *   "id" => 1,
     *   "type" => "relatedKey"
     * ]
     */
    protected function findRelatedResource(array $item, bool $reference = false): ResourceInterface
    {
        if (!isset($item['id']) || !isset($item['type'])) {
            throw (new BadRequestException('Relation item without identifiers.'))
                ->error(
                    'invalid-data',
                    ['pointer' => $this->relatedFieldName()],
                    'Relation item without `id` or `type`.'
                );
        }

        if ($this->relatedResourceRepository()->getResourceKey() !== $item['type']) {
            throw (new BadRequestException('Different resource type in delete request.'))
                ->error('invalid-data', ['pointer' => $this->relatedFieldName()], 'Type is not in sync with relation.');
        }

        if ($reference) {
            return $this->relatedResourceRepository()->getReference($item['id']);
        }

        return $this->relatedResourceRepository()->findById($item['id']);
    }

    protected function relatedLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relatedResourceRepository()->getResourceKey()
        );
    }

    protected function relatedRelationshipsLink(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s/relationships/%s',
            $this->request()->getBaseUrl(),
            $resource->getResourceKey(),
            $resource->getId(),
            $this->relatedResourceRepository()->getResourceKey()
        );
    }
}
