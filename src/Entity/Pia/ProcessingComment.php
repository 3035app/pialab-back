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
 * @ORM\Table(name="pia_processing_comment")
 */
class ProcessingComment implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(type="text")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $field;

    /**
     * @ORM\ManyToOne(targetEntity="Processing", inversedBy="comments")
     * @JMS\Groups({"Default", "Export"})
     * @JMS\Exclude()
     *
     * @var Processing
     */
    protected $processing;
    

    public function __construct(Processing $processing, string $content, string $field)
    {
        $this->processing = $processing;
        $this->content = $content;  
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
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
