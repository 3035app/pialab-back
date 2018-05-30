<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use PiaApi\Entity\Oauth\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profile")
 */
class UserProfile implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\OneToOne(targetEntity="PiaApi\Entity\Oauth\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @JMS\Exclude()
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $firstName = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $lastName = '';

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("username")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->user->getUsername();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("email")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->user->getEmail();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("roles")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->user->getRoles();
    }
}
