<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use PiaApi\Entity\Pia\Traits\HasPiaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_attachment")
 */
class Attachment implements Timestampable
{
    use ResourceTrait,
        HasPiaTrait,
        TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Pia", inversedBy="attachments")
     * @JMS\Exclude()
     *
     * @var Pia
     */
    protected $pia;

    /**
     * @ORM\Column(type="blob")
     * @JMS\SerializedName("file")
     * @JMS\Accessor(getter="getFileToBase64", setter="setFileFromBase64")
     *
     * @var string
     */
    protected $attachmentFile;
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $piaSigned = false;

    public function getFileToBase64()
    {
        if (is_string($this->attachmentFile)) {
            return $this->attachmentFile;
        }
        $string = \stream_get_contents($this->attachmentFile);

        return \base64_encode($string);
    }

    public function setFileFromBase64(string $base64)
    {
        $parts = \explode(',', $base64);
        if (count($parts) > 1) {
            $this->attachmentFile = $parts[1];
        } else {
            $this->attachmentFile = $base64;
        }
    }
}
