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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity
 * @ORM\Table(name="pia_folder")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class Folder implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(name="name", type="string")
     * @JMS\Groups({"Default"})
     *
     * @var string
     */
    private $name;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     * @JMS\Groups({"Default"})
     *
     * @var int
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     * @JMS\Groups({"Default"})
     *
     * @var int
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     * @JMS\Groups({"Default"})
     *
     * @var int
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Folder")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\Exclude()
     *
     * @var Folder
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\Groups({"Default"})
     * @JMS\MaxDepth(1)
     *
     * @var Folder
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     * @JMS\Groups({"Default"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Processing", mappedBy="folder",cascade={"remove"})
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection|Processing[]
     */
    protected $processings;

    /**
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="folders").
     * @JMS\Groups({"Full"})
     *
     * @var Structure
     */
    protected $structure;

    public function __construct(string $name, ?Structure $structure = null)
    {
        $this->name = $name;

        if ($structure !== null) {
            $this->structure = $structure;
            $structure->getFolders()->add($this);
        }

        $this->children = new ArrayCollection();
        $this->processings = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("structure_id")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return string|null
     */
    public function getStructureId(): ?string
    {
        return $this->structure !== null ? $this->structure->getId() : null;
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
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children): void
    {
        $this->children = $children;
    }

    /**
     * @return Folder
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param Folder $parent
     */
    public function setParent(?self $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Folder
     */
    public function getRoot(): ?self
    {
        return $this->root;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("isRoot")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root === null || $this->root === $this;
    }

    /**
     * @return int
     */
    public function getRgt(): int
    {
        return $this->rgt;
    }

    /**
     * @param int $rgt
     */
    public function setRgt(int $rgt): void
    {
        $this->rgt = $rgt;
    }

    /**
     * @return int
     */
    public function getLvl(): int
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     */
    public function setLvl(int $lvl): void
    {
        $this->lvl = $lvl;
    }

    /**
     * @return int
     */
    public function getLft(): int
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     */
    public function setLft(int $lft): void
    {
        $this->lft = $lft;
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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("path")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return string
     */
    public function getPath(): string
    {
        return ($this->getParent() !== null ? $this->getParent()->getPath() : '') . '/' . $this->getName();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("hierarchy")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return array
     */
    public function getHierarchy(): array
    {
        $ancestorHierarchy = $this->getParent() !== null ? $this->getParent()->getHierarchy() : [];

        $ancestorHierarchy[$this->getLvl()] = [
            'id'   => $this->getId(),
            'lvl'  => $this->getLvl(),
            'name' => $this->getName(),
        ];

        return $ancestorHierarchy;
    }

    /**
     * @return array|Processing[]
     */
    public function getProcessings(): array
    {
        return $this->processings->getValues();
    }

    /**
     * @param Processing $processing
     *
     * @throws \InvalidArgumentException
     */
    public function addProcessing(Processing $processing): void
    {
        if ($this->processings->contains($processing)) {
            throw new \InvalidArgumentException(sprintf('Processing « %s » is already in Folder « #%d »', $processing, $this->getId()));
        }
        $this->processings->add($processing);
    }

    /**
     * @param Processing $processing
     *
     * @throws \InvalidArgumentException
     */
    public function removeProcessing(Processing $processing): void
    {
        if (!$this->processings->contains($processing)) {
            throw new \InvalidArgumentException(sprintf('Processing « %s » is not in Folder « #%d »', $processing, $this->getId()));
        }
        $this->processings->removeElement($processing);
    }
}
