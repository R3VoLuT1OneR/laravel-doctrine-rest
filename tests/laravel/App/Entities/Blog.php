<?php

namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\BlogsRepository")
 * @ORM\Table(name="blog")
 */
class Blog implements ResourceInterface
{
    const STATE_DRAFT = 1;
    const STATE_PUBLISHED = 2;
    const STATE_UNPUBLISHED = 3;

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="blogs")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    protected ?User $user;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected ?string $title;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected ?string $content;

    /**
     * @ORM\OneToMany(targetEntity="BlogComment", mappedBy="blog", fetch="LAZY")
     */
    protected Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public static function getResourceKey(): string
    {
        return 'blog';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Collection|BlogComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
