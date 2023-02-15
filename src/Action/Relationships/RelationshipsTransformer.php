<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * This transformer is used for /resource/relationships/* actions.
 * In such kind of responses shouldn't have attributes.
 * So we replace transformer with this one so that no data loaded in such kind of requests.
 */
class RelationshipsTransformer extends AbstractTransformer
{
    const ATTRIBUTE_RELATIONSHIPS = '$$relationships';

    public function __construct(protected TransformerAbstract $parent) {}

    public function transform(ResourceInterface $resource): array
    {
        return [
            'id' => $resource->getId(),
            self::ATTRIBUTE_RELATIONSHIPS => true,
        ];
    }

    public function getAvailableIncludes(): array
    {
        return $this->parent->getAvailableIncludes();
    }

    public function getDefaultIncludes(): array
    {
        return $this->parent->getDefaultIncludes();
    }

    public function getCurrentScope(): ?Scope
    {
        return $this->parent->getCurrentScope();
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->parent, $name)) {
            return call_user_func_array([$this->parent, $name], $arguments);
        }
    }

    protected function item($data, $transformer, $resourceKey = null): Item
    {
        return new Item($data, new static($transformer), $resourceKey);
    }

    protected function collection($data, $transformer, $resourceKey = null): Collection
    {
        return new Collection($data, new static($transformer), $resourceKey);
    }
}
