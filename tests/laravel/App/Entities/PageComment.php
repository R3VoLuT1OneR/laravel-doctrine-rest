<?php

namespace Tests\App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\PageCommentsRepository")
 * @ORM\Table()
 */
class PageComment implements ResourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="content", type="string", length=1023, nullable=false)
     */
    protected ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    protected ?Page $page;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    protected ?User $user;

    public static function getResourceKey(): string
    {
        return 'pageComments';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPage(Page $page): static
    {
        $this->page = $page;
        return $this;
    }

    public function getPage(): Page
    {
        return $this->page;
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

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
