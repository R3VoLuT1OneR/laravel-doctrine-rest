<?php namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

/**
 * Class Role
 * @package Tests\App\Entities
 *
 * @ORM\Entity()
 * @ORM\Table(name="role")
 */
class Role implements ResourceInterface, RoleContract
{
    use HasPermissions;

    const ROOT = 1;
    const ROOT_NAME = 'Root';

    const USER = 2;
    const USER_NAME = 'User';

    public static function getResourceKey(): string
    {
        return 'roles';
    }

    /**
     * @ORM\Id();
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="permissions", type="json")
     */
    protected Collection $permissions;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles", fetch="EXTRA_LAZY")
     */
    protected Collection $users;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

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

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }
}
