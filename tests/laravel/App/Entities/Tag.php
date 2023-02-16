<?php

namespace Tests\App\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\TagsRepository")
 * @ORM\Table(name="tag")
 */
class Tag implements ResourceInterface
{
    public static function getResourceKey(): string
    {
        return 'tag';
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected ?string $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="tags", fetch="EXTRA_LAZY")
     */
    protected Collection $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
