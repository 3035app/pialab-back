<?php

namespace PiaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="pia_user")
 */
class User implements AdvancedUserInterface, \Serializable
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

    /**
     * @ORM\Column(name="creationDate", type="datetime")
     *
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @ORM\Column(name="expirationDate", type="datetime")
     *
     * @var \DateTime
     */
    protected $expirationDate;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     *
     * @var bool
     */
    protected $enabled = true;

    /**
     * @ORM\Column(name="locked", type="boolean")
     *
     * @var bool
     */
    protected $locked = false;

    public function __construct(?string $email = null, ?string $password)
    {
        $this->email = $email;
        $this->username = $email;
        $this->password = $password;
        $this->roles = ['ROLE_USER'];
        $this->creationDate = new \DateTime();
        $this->expirationDate = new \DateTimeImmutable('+1 Year');
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
            $this->creationDate,
            $this->expirationDate,
            $this->enabled,
            $this->locked,
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
            $this->creationDate,
            $this->expirationDate,
            $this->enabled,
            $this->locked,
        ) = unserialize($serialized);
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Set the value of locked
     *
     * @param bool $locked
     */
    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     */
    public function setExpirationDate(\DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return !$this->isLocked();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        return $this->expirationDate > new \DateTime();
    }
    
    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }
}
