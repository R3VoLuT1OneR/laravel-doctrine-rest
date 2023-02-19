<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Redirector;
use League\Fractal\Manager as Fractal;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Pz\LaravelDoctrine\JsonApi\Fractal\JsonApiSerializer;
use Pz\LaravelDoctrine\JsonApi\Fractal\ScopeFactory;

class ResponseFactory extends \Illuminate\Routing\ResponseFactory
{
    public function request(): JsonApiRequest
    {
        return app(JsonApiRequest::class);
    }

    public function jsonapi(?array $body, int $status = JsonApiResponse::HTTP_OK, array $header = []): JsonApiResponse
    {
        return new JsonApiResponse($body, $status, $header);
    }

    public function item(
        ResourceInterface   $resource,
        AbstractTransformer $transformer,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
        array               $meta = []
    ): JsonApiResponse
    {
        $item = (new Item($resource, $transformer, $resource->getResourceKey()))->setMeta($meta);
        $body = $this->fractal()->createData($item)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function created(
        ResourceInterface   $resource,
        AbstractTransformer $transformer,
        array               $headers = [],
        array               $meta = []
    ): JsonApiResponse
    {
        return $this->item(
            resource: $resource,
            transformer: $transformer,
            status: JsonApiResponse::HTTP_CREATED,
            headers: array_merge($headers, [
                'Location' => $this->linkToResource($resource),
            ]),
            meta: $meta
        );
    }

    public function collection(
        array $collection,
        string $resourceKey,
        AbstractTransformer $transformer,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
    ): JsonApiResponse
    {
        $collection = (new Collection($collection, $transformer, $resourceKey));
        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function query(
        QueryBuilder        $qb,
        string              $resourceKey,
        AbstractTransformer $transformer,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
        array               $meta = []
    ): JsonApiResponse
    {
        $data = new Paginator($qb, false);
        $collection = (new Collection($data, $transformer, $resourceKey))->setMeta($meta);

        if ($qb->getMaxResults()) {
            $collection->setPaginator(
                new DoctrinePaginatorAdapter(
                    $data,
                    function(int $page) {
                        // return !$resourceKey ? null : "{$request->getBaseUrl()}/$resourceKey?".http_build_query([
                        return $this->request()->getBasePath().'?'.http_build_query([
                                'page' => [
                                    'number'    => $page,
                                    'size'      => $this->request()->getMaxResults()
                                ]
                            ]);
                    }
                )
            );
        }

        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function null(int $status = JsonApiResponse::HTTP_OK, array $headers = []): JsonApiResponse
    {
        return $this->jsonapi(['data' => null], $status, $headers);
    }

    public function exception(RestException $e): JsonApiResponse
    {
        return $this->jsonapi(['errors' => $e->errors()], $e->getCode());
    }

    public function noContent($status = JsonApiResponse::HTTP_NO_CONTENT, array $headers = []): JsonApiResponse
    {
        return $this->jsonapi(null, $status, $headers);
    }

    protected function linkToResource(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s', $this->request()->getBaseUrl(), $resource->getResourceKey(), $resource->getId());
    }

    protected function fractal(): Fractal
    {
        $request = $this->request();
        $serializer = new JsonApiSerializer($request);
        $scopeFactory = new ScopeFactory($request);

        $fractal = new Fractal($scopeFactory);
        $fractal->setSerializer($serializer);

        if ($includes = $request->getInclude()) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->getExclude()) {
            $fractal->parseExcludes($excludes);
        }

        if ($fields = $request->getFields()) {
            $fractal->parseFieldsets($fields);
        }

        return $fractal;
    }
}
