<?php

namespace Tests\App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * Class BlogComment
 *
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\BlogCommentsRepository")
 * @ORM\Table(name="blog_comment")
 */
class BlogComment implements ResourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Blog", inversedBy="comments")
     * @ORM\JoinColumn(name="blog_id", nullable=false)
     */
    protected ?Blog $blog;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    protected ?User $user;

    /**
     * @ORM\Column(name="content", type="string", length=1023, nullable=false)
     */
    protected ?string $content;

    public static function getResourceKey(): string
    {
        return 'blog_comment';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBlog(Blog $blog): static
    {
        $this->blog = $blog;
        return $this;
    }

    public function getBlog(): Blog
    {
        return $this->blog;
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
