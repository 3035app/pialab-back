<?php

namespace PiaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="pia_user")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", nullable=false, unique=true)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(name="email", type="string", nullable=false, unique=true)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="password", type="string", nullable=false)
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     *
     * @var array
     */
    protected $roles;

    public function __construct(?string $email = null, ?string $password)
    {
        $this->email = $email;
        $this->username = $email;
        $this->password = $password;
        $this->roles = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->username,
            $this->password,
            $this->roles,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->username,
            $this->password,
            $this->roles,
        ) = unserialize($serialized);
    }
}
