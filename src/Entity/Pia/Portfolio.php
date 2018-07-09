<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->structures = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Collection
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }
}
