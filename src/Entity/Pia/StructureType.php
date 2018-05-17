<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_structure_type")
 */
class StructureType
{
    use ResourceTrait;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="type")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $structures;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->users = new ArrayCollection();
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
     * @return Collection
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    /**
     * @param Collection $structures
     */
    public function setStructures(Collection $structures): void
    {
        $this->structures = $structures;
    }
}
