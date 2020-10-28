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

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_processing_attachment")
 */
class ProcessingAttachment implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

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
    protected $mimeType;

    /**
     * @ORM\ManyToOne(targetEntity="Processing", inversedBy="attachments")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\Exclude()
     *
     * @var Processing
     */
    protected $processing;

    /**
     * @ORM\Column(type="text")
     * @JMS\SerializedName("file")
     * @JMS\Accessor(setter="setFileFromBase64")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $attachmentFile;

    public function setFileFromBase64(string $base64)
    {
        $parts = \explode(',', $base64);
        if (count($parts) > 1) {
            $this->attachmentFile = $parts[1];
        } else {
            $this->attachmentFile = $base64;
        }
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
}
