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
use PiaApi\Entity\Pia\Traits\HasPiaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_answer")
 */
class Answer implements Timestampable
{
    use ResourceTrait,
        HasPiaTrait,
        TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Pia", inversedBy="answers")
     * @JMS\Exclude()
     *
     * @var Pia
     */
    protected $pia;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $referenceTo = '';

    /**
     * @ORM\Column(type="json")
     * @JMS\Type("array")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var array
     */
    protected $data = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function getReferenceTo(): string
    {
        return $this->referenceTo;
    }

    public function setReferenceTo(string $ref)
    {
        $this->referenceTo = $ref;
    }
}
