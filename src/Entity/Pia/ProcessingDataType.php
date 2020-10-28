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
 * @ORM\Entity(repositoryClass="PiaApi\Repository\ProcessingDataTypeRepository")
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
    protected $reference;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $data;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $retentionPeriod;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $sensitive = false;

    /**
     * @ORM\ManyToOne(targetEntity="Processing", inversedBy="processingDataTypes")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\Exclude()
     *
     * @var Processing
     */
    protected $processing;

    public function __construct(Processing $processing, string $reference)
    {
        $this->processing = $processing;
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     */
    public function setData(?string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getRetentionPeriod(): ?string
    {
        return $this->retentionPeriod;
    }

    /**
     * @param string|null $retentionPeriod
     */
    public function setRetentionPeriod(?string $retentionPeriod): void
    {
        $this->retentionPeriod = $retentionPeriod;
    }

    /**
     * @return bool
     */
    public function isSensitive(): bool
    {
        return $this->sensitive;
    }

    /**
     * @param bool $sensitive
     */
    public function setSensitive(?bool $sensitive = true): void
    {
        $this->sensitive = $sensitive;
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
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }
}
