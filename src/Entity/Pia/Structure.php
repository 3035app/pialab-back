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
 * @ORM\Entity(repositoryClass="PiaApi\Repository\StructureRepository")
 * @ORM\Table(name="pia_structure")
 */
class Structure implements Timestampable
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
     * @ORM\ManyToOne(targetEntity="StructureType", inversedBy="structures", cascade={"persist"})
     * @JMS\Groups({"Default", "Export"})
     *
     * @var StructureType
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Portfolio", inversedBy="structures", cascade={"persist"})
     * @JMS\Groups({"Default", "Export"})
     *
     * @var Portfolio
     */
    protected $portfolio;

    /**
     * @ORM\OneToMany(targetEntity="Pia", mappedBy="structure", cascade={"remove"})
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $pias;

    /**
     * @ORM\OneToMany(targetEntity="PiaApi\Entity\Oauth\User", mappedBy="structure")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="ProcessingTemplate", mappedBy="structures")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $templates;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="structure", cascade={"persist","remove"})
     * @JMS\Groups({"Full"})
     *
     * @var Collection
     */
    protected $folders;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->pias = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->templates = new ArrayCollection();
        $this->folders = new ArrayCollection();
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
     * @return Portfolio
     */
    public function getPortfolio(): ?Portfolio
    {
        return $this->portfolio;
    }

    /**
     * @param Portfolio $portfolio
     */
    public function setPortfolio(?Portfolio $portfolio): void
    {
        $this->portfolio = $portfolio;
    }

    /**
     * @return Collection
     */
    public function getPias(): Collection
    {
        return $this->pias;
    }

    /**
     * @param Collection $pias
     */
    public function setPias(Collection $pias): void
    {
        $this->pias = $pias;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    /**
     * @return StructureType
     */
    public function getType(): ?StructureType
    {
        return $this->type;
    }

    /**
     * @param StructureType $type
     */
    public function setType(?StructureType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array|ProcessingTemplate[]
     */
    public function getTemplates(): array
    {
        return $this->templates->getValues();
    }

    /**
     * @param ProcessingTemplate $template
     *
     * @throws InvalidArgumentException
     */
    public function addTemplate(ProcessingTemplate $template): void
    {
        if ($this->templates->contains($template)) {
            throw new InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is already allowed for Structure « %s »', $template->getName(), $this->name)
            );
        }
        $template->addStructure($this);
        $this->templates->add($template);
    }

    /**
     * @param ProcessingTemplate $template
     *
     * @throws InvalidArgumentException
     */
    public function removeTemplate(ProcessingTemplate $template): void
    {
        if (!$this->templates->contains($template)) {
            throw new InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is not allowed for Structure « %s » and so cannot be disociated', $template->getName(), $this->name)
                );
        }
        $template->removeStructure($this);
        $this->templates->removeElement($template);
    }

    /**
     * @return Collection
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    /**
     * @param Collection $folders
     */
    public function setFolders(Collection $folders): void
    {
        $this->folders = $folders;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("rootFolder")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return Folder
     */
    public function getRootFolder(): ?Folder
    {
        $roots = $this->folders->filter(function (Folder $folder) {
            return $folder->isRoot();
        });

        return $roots->count() > 0 ? $roots->first() : null;
    }
}
