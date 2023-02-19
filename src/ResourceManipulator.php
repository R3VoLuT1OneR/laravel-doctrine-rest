<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pz\LaravelDoctrine\JsonApi\Exceptions\BadRequestException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataMembersException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\UnknownAttributeException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\UnknownRelationException;

class ResourceManipulator
{
    public function __construct(
        protected EntityManager $em,
    ) {}

    public function hydrateResource(
        ResourceInterface $resource,
        array $data,
        string $scope = "/data",
        bool $throwOnMissing = false
    ): ResourceInterface
    {
        if ($throwOnMissing && !isset($data['attributes']) && !isset($data['relationships'])) {
            throw new MissingDataMembersException($scope);
        }

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->hydrateAttributes($resource, $data['attributes'], "$scope/attributes");
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $this->hydrateRelationships($resource, $data['relationships'], "$scope/relationships");
        }

        return $resource;
    }

    public function hydrateAttributes(ResourceInterface $resource, array $attributes, string $scope): void
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($resource));

        foreach ($attributes as $name => $value) {
            if (!isset($metadata->reflFields[$name])) {
                throw new UnknownAttributeException("$scope/$name");
            }

            $this->setProperty($resource, $name, $value);
        }
    }

    public function hydrateRelationships(ResourceInterface $resource, array $relationships, string $scope): void
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($resource));

        foreach ($relationships as $name => $data) {
            if (!isset($metadata->associationMappings[$name])) {
                throw new UnknownRelationException("$scope/$name");
            }

            $mapping = $metadata->associationMappings[$name];

            if (!is_array($data) || !array_key_exists('data', $data)) {
                throw new MissingDataException("$scope/$name");
            }

            // To-One relation update
            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                $this->setProperty($resource, $name,
                    $this->primaryDataToResource($mapping['targetEntity'], $data['data'], "$scope/$name")
                );
            }

            // To-Many relation update
            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                $this->hydrateToManyRelation($resource, $name, $mapping['targetEntity'], $data['data'], "$scope/$name");
            }
        }
    }

    public function hydrateToManyRelation(ResourceInterface $resource, string $name, string $targetEntity, mixed $data, string $scope): void
    {
        if (!is_array($data)) {
            throw new MissingDataException($scope);
        }

        $this->setProperty($resource, $name,
            new ArrayCollection(array_map(
                fn ($item, $index) => $this->primaryDataToResource($targetEntity, $item, "$scope/$index"),
                $data,
                array_keys($data)
            ))
        );
    }

    public function primaryDataToResource(string $class, mixed $data, string $scope): ?ResourceInterface
    {
        if (is_null($data)) {
            return null;
        }

        if (is_object($data)) {
            return $data;
        }

        if (is_array($data) && isset($data['id']) && isset($data['type']) && is_string($data['type'])) {
            if (ResourceRepository::classResourceKey($class) !== $data['type']) {
                throw BadRequestException::create()
                    ->error(400, ['pointer' => $scope], sprintf(
                        'Provider relationships type "%s" is not matched with resource class.',
                        $data['type']
                    ));
            }

            if (null === ($resource = $this->em->find($class, $data['id']))) {
                throw RestException::create('Resource is not found', 404)
                    ->error(404, ['pointer' => $scope], sprintf(
                        'Resource not found by primary data %s(%s)',
                        $data['type'], $data['id']
                    ));
            }

            return $resource;
        }

        throw RestException::create('Wrong primary data provided.', 400)
            ->error(400, ['pointer' => $scope], 'Wrong primary data provided.');
    }

    public function getProperty(ResourceInterface $resource, string $property): mixed
    {
        $getter = 'get' . ucfirst($property);

        if (!method_exists($resource, $getter)) {
            throw (new BadRequestException())->error(
                    'missing-getter',
                    ['getter' => sprintf('%s::%s', ClassUtils::getClass($resource), $getter)],
                    'Missing field getter.'
                );
        }

        return $resource->$getter();
    }

    public function setProperty(ResourceInterface $resource, string $property, mixed $value): ResourceInterface
    {
        $setter = 'set' . ucfirst($property);

        if (!method_exists($resource, $setter)) {
            throw (new BadRequestException())->error(
                'missing-setter',
                ['setter' => sprintf('%s::%s', ClassUtils::getClass($resource), $setter)],
                'Missing field setter.'
            );
        }

        return $resource->$setter($value);
    }

    public function addRelationItem(object $resource, string $field, mixed $item): object
    {
        $adder = 'add' . ucfirst($field);

        if (!method_exists($resource, $adder)) {
            throw (new BadRequestException())->error(
                'missing-adder',
                ['adder' => sprintf('%s::%s', ClassUtils::getClass($resource), $adder)],
                'Missing collection adder.'
            );
        }

        return $resource->$adder($item);
    }

    public function removeRelationItem(ResourceInterface $resource, string $field, mixed $item): ResourceInterface
    {
        $remover = 'remove' . ucfirst($field);

        if (!method_exists($resource, $remover)) {
            throw (new BadRequestException())->error(
                'missing-remover',
                ['remover' => sprintf('%s::%s', ClassUtils::getClass($resource), $remover)],
                'Missing collection remover.'
            );
        }

        return $resource->$remover($item);
    }
}
