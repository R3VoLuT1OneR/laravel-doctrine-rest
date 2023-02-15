<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait CalculatesChangeSet
{
    private ?array $changeSet = null;

    abstract public function repository(): ResourceRepository;

    protected function changeSet(): array
    {
        if ($this->changeSet === null) {
            throw new \RuntimeException('Trying to get not calculated changeset. Run "calculateChangeset" first.');
        }

        return $this->changeSet;
    }

    /**
     * You can calculate change set before.
     * It must be done before the flush call.
     */
    protected function calculateChangeset(ResourceInterface $resource): void
    {
        $unitOfWork = $this->repository()->em()->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        $this->changeSet = $unitOfWork->getEntityChangeSet($resource);
    }
}
