<?php namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;
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
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", unique=true, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", unique=false, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    protected $password;

    /**
     * @var ArrayCollection|Role[]
     *
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

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = bcrypt($password);
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection|array $roles)
    {
        foreach ($roles as $role) {
            if (empty($role->getId())) {
                throw (new ValidationException())
                    ->validationError('roles', 'Can\'t add not persisted new role though User entity.');
            }
        }

        $this->roles = $roles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        $root = $this->getRoles()->filter(function(Role $role) { return $role->getId() === Role::ROOT; })->first();

        return $root instanceof Role;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addRoles(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRoles(Role $role)
    {
        $this->roles->removeElement($role);
        return $this;
    }
}
