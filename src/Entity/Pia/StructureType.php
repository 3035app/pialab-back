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

    /**
     * @ORM\ManyToMany(targetEntity="PiaTemplate", mappedBy="structureTypes")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $templates;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->users = new ArrayCollection();
        $this->templates = new ArrayCollection();
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

    /**
     * @return array|PiaTemplate[]
     */
    public function getTemplates(): array
    {
        return $this->templates->getValues();
    }

    /**
     * @param PiaTemplate $template
     *
     * @throws InvalidArgumentException
     */
    public function addTemplate(PiaTemplate $template): void
    {
        if ($this->templates->contains($template)) {
            throw new \InvalidArgumentException(sprintf('Template « %s » is already in StructureType', $template));
        }
        $template->addStructureType($this);
        $this->templates->add($template);
    }

    /**
     * @param PiaTemplate $template
     *
     * @throws InvalidArgumentException
     */
    public function removeTemplate(PiaTemplate $template): void
    {
        if (!$this->templates->contains($template)) {
            throw new \InvalidArgumentException(sprintf('Template « %s » is not in StructureType', $template));
        }
        $template->removeStructureType($this);
        $this->templates->removeElement($template);
    }
}
