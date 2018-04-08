<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;

/**
 * Class Role
 * @package Pz\LaravelDoctrine\Rest\Tests\App\Entities
 *
 * @ORM\Entity()
 * @ORM\Table(name="role")
 */
class Role implements JsonApiResource
{
    const ROOT = 1;
    const ROOT_NAME = 'Root';
    
    const USER = 2;
    const USER_NAME = 'User';

    /**
     * @return string
     */
    public static function getResourceKey()
    {
        return 'role';
    }

    /**
     * @var int
     *
     * @ORM\Id();
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
}
