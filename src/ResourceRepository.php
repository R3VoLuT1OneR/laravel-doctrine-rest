<?php

namespace Pz\LaravelDoctrine\JsonApi;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\Exceptions\NotFoundException;

use InvalidArgumentException;
use UnexpectedValueException;

class ResourceRepository extends EntityRepository
{
    const RESOURCE_TYPE_METHOD = 'getResourceKey';

    protected ?string $alias = null;

    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->verifyClassResource($this->getClassName());
    }

    public static function create(EntityManager $em, string $class): self
    {
        return new static($em, $em->getClassMetadata($class));
    }

    public function em(): EntityManager
    {
        return parent::getEntityManager();
    }

    public function resourceQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder($this->alias());
    }

    public function getResourceKey(): string
    {
        $class = $this->getClassName();
        return call_user_func(sprintf('%s::%s', $class, static::RESOURCE_TYPE_METHOD));
    }

    /**
     * Base root alias for queries.
     */
    public function alias(): string
    {
        if ($this->alias === null) {
            $shortName = $this->getClassMetadata()->getReflectionClass()->getShortName();
            $this->alias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        }

        return $this->alias;
    }

    public function findById(string|int $id): ResourceInterface
    {
        if (null === ($entity = $this->find($id))) {
            throw new NotFoundException($id, $this->getResourceKey());
        }

        return $entity;
    }

    public function getReference(string|int $id): ResourceInterface
    {
        $this->em()->getReference($this->getClassName(), $id);
    }

    /**
     * Make sure class is implements resource interface.
     */
    private function verifyClassResource(string $class): void
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('%s - is not a class', $class));
        }

        if (!isset(class_implements($class)[ResourceInterface::class])) {
            throw new UnexpectedValueException(sprintf(
                '%s - not implements %s', $class, ResourceInterface::class
            ));
        }
    }
}
