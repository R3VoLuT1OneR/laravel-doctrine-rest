<?php

namespace Pz\LaravelDoctrine\JsonApi\Fractal;

use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsTransformer;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class JsonApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    public function __construct(protected JsonApiRequest $request)
    {
        parent::__construct($request->getBaseUrl());
    }

    public function request(): JsonApiRequest
    {
        return $this->request;
    }

    public function item($resourceKey, array $data, bool $includeAttributes = true): array
    {
        $item = parent::item($resourceKey, $data);

        if ($item['data']['attributes'][RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS] ?? false) {
            unset($item['data']['attributes']);
        }

        return $item;
    }
}
