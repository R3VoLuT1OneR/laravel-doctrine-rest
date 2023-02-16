<?php namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Pz\LaravelDoctrine\JsonApi\Exceptions\ValidationException;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use LaravelDoctrine\ORM\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Passwords\CanResetPassword;

/**
 * Class User
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, ResourceInterface
{
    use Authenticatable, Authorizable, CanResetPassword;

    public static function getResourceKey(): string
    {
        return 'users';
    }

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="email", type="string", unique=true, nullable=false)
     */
    protected ?string $email;

    /**
     * @ORM\Column(name="name", type="string", unique=false, nullable=false)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *     joinColumns={
     *         @ORM\JoinColumn(
     *             name="user_id",
     *             referencedColumnName="id",
     *             nullable=false
     *         )
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(
     *             name="role_id",
     *             referencedColumnName="id",
     *             nullable=false
     *         )
     *     }
     * )
     */
    protected Collection $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPassword(string $password): static
    {
        $this->password = bcrypt($password);
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection|array $roles): static
    {
        foreach ($roles as $role) {
            if (empty($role->getId())) {
                throw (new ValidationException())
                    ->validationError('roles', 'Can\'t add not persisted new role though User entity.');
            }
        }

        $this->roles = is_array($roles) ? new ArrayCollection($roles) : $roles;
        return $this;
    }

    public function addRoles(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRoles(Role $role): static
    {
        $this->roles->removeElement($role);
        return $this;
    }

    public function isRoot(): bool
    {
        return $this->getRoles()
            ->contains(EntityManager::getReference(Role::class, Role::ROOT));
    }
}
