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
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_processing_data_type")
 */
class ProcessingDataType
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
    protected $description;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $initialDataRetentionPeriod;

    /**
     * @ORM\OneToMany(targetEntity="ProcessedDataType", mappedBy="processingDataType")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(2)
     *
     * @var Collection|ProcessedDataType[]
     */
    protected $processedDataTypes;

    public function __construct()
    {
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
    public function getInitialDataRetentionPeriod(): string
    {
        return $this->initialDataRetentionPeriod;
    }

    /**
     * @param string $initialDataRetentionPeriod
     */
    public function setInitialDataRetentionPeriod(string $initialDataRetentionPeriod): void
    {
        $this->initialDataRetentionPeriod = $initialDataRetentionPeriod;
    }

    /**
     * @return array|ProcessedDataType[]
     */
    public function getProcessedDataTypes(): array
    {
        return $this->processedDataTypes->getValues();
    }

    /**
     * @param ProcessedDataType $processedDataType
     *
     * @throws \InvalidArgumentException
     */
    public function addProcessedDataType(ProcessedDataType $processedDataType): void
    {
        if ($this->processedDataTypes->contains($processedDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessedDataType « %s » is already linked to ProcessingDataType « #%d »', $processedDataType, $this->getId()));
        }
        $this->processedDataTypes->add($processedDataType);
    }

    /**
     * @param ProcessedDataType $processedDataType
     *
     * @throws \InvalidArgumentException
     */
    public function removeProcessedDataType(ProcessedDataType $processedDataType): void
    {
        if (!$this->processedDataTypes->contains($processedDataType)) {
            throw new \InvalidArgumentException(sprintf('ProcessedDataType « %s » is not linked to ProcessingDataType « #%d »', $processedDataType, $this->getId()));
        }
        $this->processedDataTypes->removeElement($processedDataType);
    }
}
