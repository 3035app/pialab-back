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
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;

/**
 * @ORM\Entity(repositoryClass="PiaApi\Repository\ProcessingRepository")
 * @ORM\Table(name="pia_processing")
 */
class Processing
{
    use
        ResourceTrait,
        TimestampableEntity;

    const STATUS_DOING = 0;
    const STATUS_ARCHIVED = 1;

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
     * @ORM\Column(type="integer")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $status = self::STATUS_DOING;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $lifeCycle;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $storage;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $standards;

    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $nonEuTransfer;

    /**
     * @ORM\OneToMany(targetEntity="ProcessingDataType", mappedBy="processing")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection|ProcessingDataType[]
     */
    protected $processingDataTypes;

    /**
     * @ORM\OneToMany(targetEntity="Pia", mappedBy="processing")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\Exclude()
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

    public function __construct(
        string $name,
        Folder $folder,
        string $author,
        string $controllers
    ) {
        $this->name = $name;
        $this->folder = $folder;
        $this->author = $author;
        $this->controllers = $controllers;

        $this->processingDataTypes = new ArrayCollection();
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
    public function getLifeCycle(): string
    {
        return $this->lifeCycle;
    }

    /**
     * @param string $lifeCycle
     */
    public function setLifeCycle(string $lifeCycle): void
    {
        $this->lifeCycle = $lifeCycle;
    }

    /**
     * @return string
     */
    public function getDataMedium(): string
    {
        return $this->dataMedium;
    }

    /**
     * @param string $dataMedium
     */
    public function setDataMedium(string $dataMedium): void
    {
        $this->dataMedium = $dataMedium;
    }

    /**
     * @return string
     */
    public function getStandards(): string
    {
        return $this->standards;
    }

    /**
     * @param string $standards
     */
    public function setStandards(string $standards): void
    {
        $this->standards = $standards;
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
     * @return array|ProcessingDataType[]
     */
    public function getProcessingDataTypes(): array
    {
        return $this->processingDataTypes->getValues();
    }

    /**
     * @param ProcessingDataType $processingDataType
     *
     * @throws \InvalidArgumentException
     */
    public function addProcessingDataType(ProcessingDataType $processingDataType): void
    {
        if ($this->processingDataTypes->contains($processingDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessingDataType « %s » already belongs to Processing « #%d »', $processingDataType->getId(), $this->getId()));
        }
        $this->processingDataTypes->add($processingDataType);
    }

    /**
     * @param ProcessingDataType $processingDataType
     *
     * @throws \InvalidArgumentException
     */
    public function removeProcessingDataType(ProcessingDataType $processingDataType): void
    {
        if (!$this->processingDataTypes->contains($processingDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessingDataType « %s » does not belong to Processing « #%d »', $processingDataType->getId(), $this->getId()));
        }
        $this->processingDataTypes->removeElement($processingDataType);
    }

    /**
     * @JMS\VirtualProperty("piasCount")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return int
     */
    public function getPiasCount(): int
    {
        return $this->pias->count();
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
            throw new \InvalidArgumentException(sprintf('Pia « %s » is already linked to Processing « #%d »', $pia->getId(), $this->getId()));
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
            throw new \InvalidArgumentException(sprintf('Pia « %s » is not linked to Processing « #%s »', $pia->getId(), $this->getId()));
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

    /**
     * @return string
     */
    public function getNonEuTransfer(): ?string
    {
        return $this->nonEuTransfer;
    }

    /**
     * @param string $nonEuTransfer
     */
    public function setNonEuTransfer(?string $nonEuTransfer): void
    {
        $this->nonEuTransfer = $nonEuTransfer;
    }

    /**
     * @return string
     */
    public function getStorage() : ?string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     */
    public function setStorage(?string $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        if ($status !== self::STATUS_DOING && $status !== self::STATUS_ARCHIVED) {
            throw new \InvalidArgumentException(sprintf('Status « %d » is not valid', $status));
        }
        $this->status = $status;
    }
}
