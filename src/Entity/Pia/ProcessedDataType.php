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

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_processing__pia_processing_data_type")
 */
class ProcessedDataType
{
    use ResourceTrait;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $specificDataRetentionPeriod;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $sensitiveData = false;

    /**
     * @ORM\ManyToOne(targetEntity="Processing", inversedBy="processedDataTypes")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(1)
     *
     * @var Processing
     */
    protected $processing;

    /**
     * @ORM\ManyToOne(targetEntity="ProcessingDataType", inversedBy="processedDataTypes")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\MaxDepth(1)
     *
     * @var ProcessingDataType
     */
    protected $processingDataType;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getSpecificDataRetentionPeriod(): string
    {
        return $this->specificDataRetentionPeriod;
    }

    /**
     * @param string $specificDataRetentionPeriod
     */
    public function setSpecificDataRetentionPeriod(string $specificDataRetentionPeriod): void
    {
        $this->specificDataRetentionPeriod = $specificDataRetentionPeriod;
    }

    /**
     * @return bool
     */
    public function isSensitiveData(): bool
    {
        return $this->sensitiveData;
    }

    /**
     * @param bool $sensitiveData
     */
    public function setSensitiveData(?bool $sensitiveData = true): void
    {
        $this->sensitiveData = $sensitiveData;
    }

    /**
     * @return Processing
     */
    public function getProcessing(): Processing
    {
        return $this->processing;
    }

    /**
     * @param Processing $processing
     */
    public function setProcessing(Processing $processing): void
    {
        $this->processing = $processing;
    }

    /**
     * @return ProcessingDataType
     */
    public function getProcessingDataType(): ProcessingDataType
    {
        return $this->processingDataType;
    }

    /**
     * @param ProcessingDataType $processingDataType
     */
    public function setProcessingDataType(ProcessingDataType $processingDataType): void
    {
        $this->processingDataType = $processingDataType;
    }
}
