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
use PiaApi\Entity\Pia\Traits\HasPiaTrait;
use PiaApi\Entity\Oauth\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_profile")
 */
class UserProfile implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\OneToOne(targetEntity="PiaApi\Entity\Oauth\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\Column(type="json")
     * @JMS\Type("array")
     *
     * @var array
     */
    protected $piaRoles = [];

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getPiaRoles(): array
    {
        return $this->piaRoles;
    }

    /**
     * @param array $piaRoles
     */
    public function setPiaRoles(array $piaRoles): void
    {
        $this->piaRoles = $piaRoles;
    }
}
