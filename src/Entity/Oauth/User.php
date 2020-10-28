<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Oauth;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\UserProfile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

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
     * @var \DateTimeInterface
     */
    protected $creationDate;

    /**
     * @ORM\Column(name="expirationDate", type="datetime")
     *
     * @JMS\Type("DateTime")
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
     * Encrypted password. Must be persisted.
     *
     * @JMS\Exclude()
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\OneToOne(targetEntity="PiaApi\Entity\Pia\UserProfile", mappedBy="user", cascade={"persist", "remove"})
     *
     * @JMS\MaxDepth(2)
     *
     * @var bool
     */
    protected $profile;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="users")
     *
     * @JMS\MaxDepth(1)
     *
     * @var Client
     */
    protected $application;

    /**
     * @ORM\ManyToOne(targetEntity="PiaApi\Entity\Pia\Structure", inversedBy="users")
     *
     * @JMS\MaxDepth(1)
     *
     * @var Structure
     */
    protected $structure;

    /**
     * @ORM\ManyToMany(targetEntity="PiaApi\Entity\Pia\Portfolio", inversedBy="users")
     * @ORM\JoinTable(
     *      name="pia_users__portfolios",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_portfolio_id", referencedColumnName="id")}
     * )
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $portfolios;

    public function __construct(?string $email = null)
    {
        $this->email = $email;
        $this->username = $email;
        $this->roles = ['ROLE_USER'];
        $this->creationDate = new \DateTime();
        $this->expirationDate = new \DateTime('+1 Year');
        $this->enabled = true;
        $this->profile = new UserProfile();
        $this->portfolios = new ArrayCollection();
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

    /**
     * @return array
     */
    public function getPortfolios(): array
    {
        return $this->portfolios->getValues();
    }

    public function setPortfolios(iterable $portfolios): void
    {
        foreach ($portfolios as $portfolio) {
            $this->addPortfolio($portfolio);
        }
    }

    public function addPortfolio(Portfolio $portfolio)
    {
        $this->portfolios->add($portfolio);
    }

    public function removePortfolio(Portfolio $portfolio): void
    {
        $this->portfolios->removeElement($portfolio);
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
     * @return bool
     */
    public function hasStructure(): bool
    {
        return $this->structure !== null;
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

    public function getPortfolioStructures(): array
    {
        $allStructures = new ArrayCollection();

        foreach ($this->portfolios as $portfolio) {
            $structures = $portfolio->getStructures();
            foreach ($structures as $structure) {
                if (!$allStructures->contains($structure)) {
                    $allStructures->add($structure);
                }
            }
        }

        return $allStructures->toArray();
    }
}
