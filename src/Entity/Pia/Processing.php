<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_processing")
 */
class Processing
{
    use ResourceTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $lifeCycleDescription;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $dataMediumDescription;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $standardsDescription;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $processors;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $controllers;

    /**
     * @ORM\OneToMany(targetEntity="ProcessedDataType", mappedBy="processing")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection|ProcessedDataType[]
     */
    protected $processedDataTypes;

    /**
     * @ORM\OneToMany(targetEntity="Pia", mappedBy="processing")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(1)
     *
     * @var Collection|Pia[]
     */
    protected $pias;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="processings")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(1)
     *
     * @var Folder
     */
    protected $folder;

    public function __construct()
    {
        $this->processedDataTypes = new ArrayCollection();
        $this->pias = new ArrayCollection();
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
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLifeCycleDescription(): string
    {
        return $this->lifeCycleDescription;
    }

    /**
     * @param string $lifeCycleDescription
     */
    public function setLifeCycleDescription(string $lifeCycleDescription): void
    {
        $this->lifeCycleDescription = $lifeCycleDescription;
    }

    /**
     * @return string
     */
    public function getDataMediumDescription(): string
    {
        return $this->dataMediumDescription;
    }

    /**
     * @param string $dataMediumDescription
     */
    public function setDataMediumDescription(string $dataMediumDescription): void
    {
        $this->dataMediumDescription = $dataMediumDescription;
    }

    /**
     * @return string
     */
    public function getStandardsDescription(): string
    {
        return $this->standardsDescription;
    }

    /**
     * @param string $standardsDescription
     */
    public function setStandardsDescription(string $standardsDescription): void
    {
        $this->standardsDescription = $standardsDescription;
    }

    /**
     * @return string
     */
    public function getProcessors(): string
    {
        return $this->processors;
    }

    /**
     * @param string $processors
     */
    public function setProcessors(string $processors): void
    {
        $this->processors = $processors;
    }

    /**
     * @return string
     */
    public function getControllers(): string
    {
        return $this->controllers;
    }

    /**
     * @param string $controllers
     */
    public function setControllers(string $controllers): void
    {
        $this->controllers = $controllers;
    }

    /**
     * @return array|ProcessedDataType[]
     */
    public function getProcessedDataTypes(): array
    {
        return $this->processedDataType->getValues();
    }

    /**
     * @param ProcessedDataType $processedDataType
     *
     * @throws \InvalidArgumentException
     */
    public function addProcessedDataType(ProcessedDataType $processedDataType): void
    {
        if ($this->processedDataType->contains($processedDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessedDataType « %s » already belongs to Processing « #%d »', $processedDataType, $this->getId()));
        }
        $this->processedDataType->add($processedDataType);
    }

    /**
     * @param ProcessedDataType $processedDataType
     *
     * @throws \InvalidArgumentException
     */
    public function removeProcessedDataType(ProcessedDataType $processedDataType): void
    {
        if (!$this->processedDataType->contains($processedDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessedDataType « %s » does not belong to Processing « #%d »', $processedDataType, $this->getId()));
        }
        $this->processedDataType->removeElement($processedDataType);
    }

    /**
     * @return array|Pia[]
     */
    public function getPias(): array
    {
        return $this->pias->getValues();
    }

    /**
     * @param Pia $pia
     *
     * @throws \InvalidArgumentException
     */
    public function addPia(Pia $pia): void
    {
        if ($this->pias->contains($pia)) {
            throw new \InvalidArgumentException(sprintf('Pia « %s » is already linked to Processing « #%d »', $pia, $this->getId()));
        }
        $this->pias->add($pia);
    }

    /**
     * @param Pia $pia
     *
     * @throws \InvalidArgumentException
     */
    public function removePia(Pia $pia): void
    {
        if (!$this->pias->contains($pia)) {
            throw new \InvalidArgumentException(sprintf('Pia « %s » is not linked to Processing « #%s »', $pia, $this->getId()));
        }
        $this->pias->removeElement($pia);
    }

    /**
     * @return Folder
     */
    public function getFolder(): Folder
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder): void
    {
        $this->folder = $folder;
    }
}
