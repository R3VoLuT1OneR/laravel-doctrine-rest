<?php

namespace Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery\MockInterface;
use Pz\LaravelDoctrine\JsonApi\Exceptions\JsonApiException;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;

class ResourceRepositoryTest extends TestCase
{
    public function testFindByIdentifierInvalidEntity()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(sprintf('stdClass - not implements %s', ResourceInterface::class));

        $id = 777;

        /** @var EntityManager|MockInterface $emMock */
        $emMock = m::mock(EntityManager::class);
        $emMock->shouldReceive('find')
            ->withArgs([\stdClass::class, $id, null, null])
            ->andReturn(new \stdClass());

        /** @var ClassMetadata|MockInterface $classMetadata */
        $classMetadata = m::mock(ClassMetadata::class);
        $classMetadata->name = \stdClass::class;

        $repository = new ResourceRepository($emMock, $classMetadata);
        $this->assertEquals('', $repository->getResourceKey());
        $repository->findById($id);
    }
}
