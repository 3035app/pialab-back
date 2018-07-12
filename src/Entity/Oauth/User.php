<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Oauth;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\UserProfile;

/**
 * @ORM\Entity(repositoryClass="PiaApi\Repository\UserRepository")
 * @ORM\Table(name="pia_user")
 */
class User extends BaseUser implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="locked", type="boolean")
     *
     * @var bool
     */
    protected $locked = false;

    /**
     * @ORM\OneToOne(targetEntity="PiaApi\Entity\Pia\UserProfile", mappedBy="user", cascade={"persist", "remove"})
     *
     * @var bool
     */
    protected $profile;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="users")
     *
     * @var Client
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="PiaApi\Entity\Pia\Structure", inversedBy="users")
     *
     * @var Structure
     */
    protected $structure;

    public function __construct(?string $email = null)
    {
        $this->email = $email;
        $this->username = $email;
        $this->roles = ['ROLE_USER'];
        $this->creationDate = new \DateTime();
        $this->expirationDate = new \DateTimeImmutable('+1 Year');
        $this->enabled = true;
        $this->setProfile(new UserProfile());
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
    public function setEmail($email)
    {
        $this->email = $email;
        if ($this->username === null) {
            $this->username = $email;
        }
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
    public function setPassword($password)
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

    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        $this->roles = array_values($this->roles);
    }

    public function removeRole($role)
    {
        $key = array_search($role, $this->roles);

        if ($key !== false) {
            unset($this->roles[$key]);
        }

        $this->roles = array_values($this->roles);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function eraseCredentials()
    {
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
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Set the value of locked.
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

    /**
     * @return Client
     */
    public function getApplication(): ?Client
    {
        return $this->application;
    }

    /**
     * @param Client $application
     */
    public function setApplication(?Client $application): void
    {
        $this->application = $application;
    }

    /**
     * @return Structure
     */
    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     */
    public function setStructure(?Structure $structure): void
    {
        $this->structure = $structure;
    }

    /**
     * @return UserProfile
     */
    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    /**
     * @param UserProfile $profile
     */
    public function setProfile(?UserProfile $profile): void
    {
        $profile->setUser($this);
        $this->profile = $profile;
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
            $this->application,
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
            $this->application) = unserialize($serialized);
    }
}
