<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pz\LaravelDoctrine\JsonApi\Exceptions\BadRequestException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\MissingDataMembersException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\UnknownAttributeException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\UnknownRelationException;

class ResourceManipulator
{
    public function __construct(
        protected EntityManager $em,
    ) {}

    public function hydrateResource(ResourceInterface|string $resource, array $data, string $scope = "/data"): ResourceInterface
    {
        if (!isset($data['attributes']) && !isset($data['relationships'])) {
            throw new MissingDataMembersException($scope);
        }

        if (is_string($resource)) {
            $resource = new $resource;
        }

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $resource = $this->hydrateAttributes($resource, $data['attributes'], "$scope/attributes");
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $resource = $this->hydrateRelationships($resource, $data['relationships'], "$scope/relationships");
        }

        return $resource;
    }

    public function hydrateAttributes(ResourceInterface $entity, array $attributes, string $scope): ResourceInterface
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($entity));

        foreach ($attributes as $name => $value) {
            if (!isset($metadata->reflFields[$name])) {
                throw new UnknownAttributeException("$scope/$name");
            }

            $this->setProperty($entity, $name, $value);
        }

        return $entity;
    }

    public function hydrateRelationships(ResourceInterface $entity, array $relationships, string $scope): ResourceInterface
    {
        $metadata = $this->em->getClassMetadata(ClassUtils::getClass($entity));

        foreach ($relationships as $name => $data) {
            if (!isset($metadata->associationMappings[$name])) {
                throw new UnknownRelationException("$scope/$name");
            }

            $mapping = $metadata->associationMappings[$name];

            if (!is_array($data) || !array_key_exists('data', $data)) {
                throw new MissingDataException("$scope/$name");
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                $this->setProperty($entity, $name,
                    $this->hydrateRelationData($mapping['targetEntity'], $data['data'], "$scope/$name")
                );
            }

            if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                $this->hydrateToManyRelation($entity, $name, $mapping['targetEntity'], $data['data'], "$scope/$name");
            }
        }

        return $entity;
    }

    private function hydrateToManyRelation(object $entity, string $name, string $targetEntity, mixed $data, string $scope): void
    {
        if (!is_array($data)) {
            throw new MissingDataException($scope);
        }

        $this->setProperty($entity, $name,
            new ArrayCollection(array_map(
                function($item, $index) use ($targetEntity, $scope) {
                    return $this->hydrateRelationData($targetEntity, $item, "$scope/$index");
                },
                $data,
                array_keys($data)
            ))
        );
    }

    private function hydrateRelationData(string $class, mixed $data, string $scope): ?object
    {
        if (is_null($data)) {
            return null;
        }

        if (is_object($data)) {
            return $data;
        }

        if (is_scalar($data)) {
            return $this->em->getReference($class, $data);
        }

        if (!is_array($data)) {
            throw new MissingDataException($scope);
        }

        if (isset($data['id']) && isset($data['type'])) {
            return $this->em->getReference($class, $data['id']);
        } else {
            return $this->hydrateResource(new $class, $data, "$scope/");
        }
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

    public function setProperty(object $resource, string $property, mixed $value): object
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

    public static function addRelationItem(object $resource, string $field, mixed $item): object
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

    public static function removeRelationItem(object $resource, string $field, mixed $item): object
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
