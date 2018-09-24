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
    const STATUS_UNDER_VALIDATION = 1;
    const STATUS_VALIDATED = 2;
    const STATUS_ARCHIVED = 3;

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
     * @ORM\Column(type="integer", options={"default": Processing::STATUS_DOING})
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $status = ProcessingStatus::STATUS_DOING;

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
    protected $designatedController;

    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $recipients;
  
    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $contextOfImplementation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $lawfulness;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $minimization;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $rightsGuarantee;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $exactness;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string|null
     */
    protected $consent;

    /**
     * @ORM\OneToMany(targetEntity="ProcessingDataType", mappedBy="processing", cascade={"remove"})
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection|ProcessingDataType[]
     */
    protected $processingDataTypes;

    /**
     * @ORM\OneToMany(targetEntity="Pia", mappedBy="processing", cascade={"persist"})
     * @JMS\Groups({"Default", "Export"})
     * @JMS\Exclude()
     *
     * @var Collection|Pia[]
     */
    protected $pias;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="processings")
     * @JMS\Groups({"Default"})
     * @JMS\MaxDepth(1)
     *
     * @var Folder
     */
    protected $folder;

    /**
     * @ORM\ManyToOne(targetEntity="ProcessingTemplate", inversedBy="processings")
     * @JMS\Groups({"Full"})
     *
     * @var ProcessingTemplate
     */
    protected $template;

    public function __construct(
        string $name,
        Folder $folder,
        string $author,
        string $designatedController
    ) {
        $this->name = $name;
        $this->folder = $folder;
        $this->author = $author;
        $this->designatedController = $designatedController;

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description = null): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getLifeCycle(): ?string
    {
        return $this->lifeCycle;
    }

    /**
     * @param string $lifeCycle
     */
    public function setLifeCycle(?string $lifeCycle = null): void
    {
        $this->lifeCycle = $lifeCycle;
    }

    /**
     * @return string|null
     */
    public function getDataMedium(): ?string
    {
        return $this->dataMedium;
    }

    /**
     * @param string $dataMedium
     */
    public function setDataMedium(?string $dataMedium = null): void
    {
        $this->dataMedium = $dataMedium;
    }

    /**
     * @return string
     */
    public function getStandards(): ?string
    {
        return $this->standards;
    }

    /**
     * @param string $standards
     */
    public function setStandards(?string $standards): void
    {
        $this->standards = $standards;
    }

    /**
     * @return string|null
     */
    public function getProcessors(): ?string
    {
        return $this->processors;
    }

    /**
     * @param string $processors
     */
    public function setProcessors(?string $processors = null): void
    {
        $this->processors = $processors;
    }

    /**
     * @return string
     */
    public function getControllers(): ?string
    {
        return $this->controllers;
    }

    /**
     * @param string $controllers
     */
    public function setControllers(?string $controllers = null): void
    {
        $this->controllers = $controllers;
    }
    
     /**
     * @return string
     */
    public function getLawfulness(): ?string
    {
        return $this->lawfulness;
    }

    /**
     * @param string $lawfulness
     */
    public function setLawfulness(?string $lawfulness = null): void
    {
        $this->lawfulness = $lawfulness;
    }

    /**
     * @return string
     */
    public function getMinimization(): ?string
    {
        return $this->minimization;
    }

    /**
     * @param string $minimization
     */
    public function setMinimization(?string $minimization = null): void
    {
        $this->minimization = $minimization;
    }

    /**
     * @return string
     */
    public function getRightsGuarantee(): ?string
    {
        return $this->rightsGuarantee;
    }

    /**
     * @param string $rightsGuarantee
     */
    public function setRightsGuarantee(?string $rightsGuarantee = null): void
    {
        $this->rightsGuarantee = $rightsGuarantee;
    }

    /**
     * @return string
     */
    public function getExactness(): ?string
    {
        return $this->exactness;
    }

    /**
     * @param string $exactness
     */
    public function setExactness(?string $exactness = null): void
    {
        $this->exactness = $exactness;
    }

    /**
     * @return string
     */
    public function getConsent(): ?string
    {
        return $this->consent;
    }

    /**
     * @param string $consent
     */
    public function setConsent(?string $consent = null): void
    {
        $this->consent = $consent;
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
    public function getStorage(): ?string
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
     * @return string
     */
    public function getStatusName(): string
    {
        return ProcessingStatus::getStatusName($this->status);
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        if (!in_array($status, [
            self::STATUS_DOING,
            self::STATUS_UNDER_VALIDATION,
            self::STATUS_VALIDATED,
            self::STATUS_ARCHIVED,
        ])) {
            throw new \InvalidArgumentException(sprintf('Status « %d » is not valid', $status));
        }
        $this->status = $status;
    }

    /**
     * @return ProcessingTemplate
     */
    public function getTemplate(): ?ProcessingTemplate
    {
        return $this->template;
    }

    /**
     * @param ProcessingTemplate $template
     */
    public function setTemplate(?ProcessingTemplate $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getDesignatedController(): string
    {
        return $this->designatedController;
    }

    /**
     * @param string $designatedController
     */
    public function setDesignatedController(string $designatedController): void
    {
        $this->designatedController = $designatedController;
    }

    /**
     * @return string|null
     */
    public function getRecipients(): ?string
    {
        return $this->recipients;
    }

    /**
     * @param string|null $recipients
     */
    public function setRecipients(?string $recipients = null): void
    {
        $this->recipients = $recipients;
    }
  
    /**
     * @return string|null
     */
    public function getContextOfImplementation(): ?string
    {
        return $this->contextOfImplementation;
    }

    /**
     * @param string|null $contextOfImplementation
     */
    public function setContextOfImplementation(?string $contextOfImplementation = null): void
    {
        $this->contextOfImplementation = $contextOfImplementation;
    }
}
