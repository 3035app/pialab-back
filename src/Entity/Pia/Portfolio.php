<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\Traits\ResourceTrait;

/**
 * @ORM\Entity(repositoryClass="PiaApi\Repository\PortfolioRepository")
 * @ORM\Table(name="pia_portfolio")
 */
class Portfolio implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="portfolio")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $structures;

    /**
     * @ORM\ManyToMany(targetEntity="PiaApi\Entity\Oauth\User", mappedBy="portfolios")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $users;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->structures = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getStructures(): array
    {
        return $this->structures->getValues();
    }

    /**
     * @return array
     */
    public function setStructures(iterable $structures): void
    {
        foreach ($structures as $structure) {
            $this->addStructure($structure);
        }
    }

    public function addStructure(Structure $structure)
    {
        $structure->setPortfolio($this);
        $this->structures->add($structure);
    }

    public function removeStructure(Structure $structure)
    {
        $structure->setPortfolio(null);
        $this->structures->removeElement($structure);
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users->getValues();
    }

    public function setUsers(array $users): void
    {
        $this->users = new ArrayCollection($users);
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    public function removeUser(User $user): array
    {
        $this->users->removeElement($user);
    }
}
