<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Exceptions\BadRequestException;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait RelatedActionTrait
{
    protected ResourceRepository $related;
    protected string $field;
    protected string $mappedBy;

    public function related(): ResourceRepository
    {
        return $this->related;
    }

    /**
     * `field` on base entity that identify relation.
     */
    public function field(): string
    {
        return $this->field;
    }

    public function mappedBy(): string
    {
        return $this->mappedBy;
    }

    protected function getRelatedEntity(array $item): object
    {
        if (!isset($item['id']) || !isset($item['type'])) {
            throw (new BadRequestException('Relation item without identifiers.'))
                ->error(
                    'invalid-data',
                    ['pointer' => $this->field()],
                    'Relation item without `id` or `type`.'
                );
        }

        if ($this->related()->getResourceKey() !== $item['type']) {
            throw (new BadRequestException('Different resource type in delete request.'))
                ->error('invalid-data', ['pointer' => $this->field()], 'Type is not in sync with relation.');
        }

        return $this->related()->findById($item['id']);
    }
}
