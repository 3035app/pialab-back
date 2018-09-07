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
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="PiaApi\Repository\ProcessingTemplateRepository")
 * @ORM\Table(name="pia_template")
 */
class ProcessingTemplate implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="json")
     * @JMS\Exclude()
     *
     * @var string
     */
    protected $data;

    /**
     * @ORM\Column(type="string")
     * @JMS\Exclude()
     *
     * @var string
     */
    protected $importedFileName;

    /**
     * @ORM\OneToMany(targetEntity="Processing", mappedBy="template")
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $processings;

    /**
     * @ORM\ManyToMany(targetEntity="Structure", inversedBy="templates")
     * @ORM\JoinTable(
     *      name="pia_templates__structures",
     *      joinColumns={@ORM\JoinColumn(name="structure_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="structure_pia_template_id", referencedColumnName="id")}
     * )
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $structures;

    /**
     * @ORM\ManyToMany(targetEntity="StructureType", inversedBy="templates")
     * @ORM\JoinTable(
     *      name="pia_templates__structure_types",
     *      joinColumns={@ORM\JoinColumn(name="structure_type_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="structure_type_pia_template_id", referencedColumnName="id")}
     * )
     * @JMS\Exclude()
     *
     * @var Collection
     */
    protected $structureTypes;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->processings = new ArrayCollection();
        $this->structures = new ArrayCollection();
        $this->structureTypes = new ArrayCollection();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     */
    public function setEnabled(?bool $enabled = true): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
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
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getProcessings(): Collection
    {
        return $this->processings;
    }

    /**
     * @param Collection $processings
     */
    public function setProcessings(Collection $processings): void
    {
        $this->processings = $processings;
    }

    /**
     * @return string
     */
    public function getImportedFileName(): string
    {
        return $this->importedFileName;
    }

    /**
     * @param string $importedFileName
     */
    public function setImportedFileName(string $importedFileName): void
    {
        $this->importedFileName = $importedFileName;
    }

    /**
     * Add file to the template.
     *
     * @param UploadedFile $file
     */
    public function addFile(UploadedFile $file): void
    {
        $content = file_get_contents($file->getPathname());
        $this->setData($content);
        $this->setImportedFileName($file->getClientOriginalName());
    }

    /**
     * @return array|Structure[]
     */
    public function getStructures(): array
    {
        return $this->structures->getValues();
    }

    /**
     * @param Structure $structure
     *
     * @throws InvalidArgumentException
     */
    public function addStructure(Structure $structure): void
    {
        if ($this->structures->contains($structure)) {
            throw new InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is already allowed for Structure « %s »', $this->name, $structure->getName())
            );
        }
        $this->structures->add($structure);
    }

    /**
     * @param Structure $structure
     *
     * @throws InvalidArgumentException
     */
    public function removeStructure(Structure $structure): void
    {
        if (!$this->structures->contains($structure)) {
            throw new InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is not allowed for Structure « %s » and so cannot be disociated', $this->name, $structure->getName())
            );
        }
        $this->structures->removeElement($structure);
    }

    /**
     * @return array|StructureType[]
     */
    public function getStructureTypes(): array
    {
        return $this->structureTypes->getValues();
    }

    /**
     * @param StructureType $structureType
     *
     * @throws InvalidArgumentException
     */
    public function addStructureType(StructureType $structureType): void
    {
        if ($this->structureTypes->contains($structureType)) {
            throw new \InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is already allowed for StructureType « %s »', $this->name, $structureType)
            );
        }
        $this->structureTypes->add($structureType);
    }

    /**
     * @param StructureType $structureType
     *
     * @throws InvalidArgumentException
     */
    public function removeStructureType(StructureType $structureType): void
    {
        if (!$this->structureTypes->contains($structureType)) {
            throw new \InvalidArgumentException(
                sprintf('The ProcessingTemplate « %s » is not allowed for StructureType « %s » and so cannot be disociated', $this->name, $structureType)    
            );
        }
        $this->structureTypes->removeElement($structureType);
    }
}
