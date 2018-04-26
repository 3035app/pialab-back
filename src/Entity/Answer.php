<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity;

use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Traits\ResourceTrait;
use PiaApi\Entity\Traits\HasPiaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="answer")
 */
class Answer implements Timestampable
{
    use ResourceTrait,
        HasPiaTrait,
        TimestampableEntity;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $referenceTo = '';
    /**
     * @ORM\Column(type="json")
     * @JMS\Type("array")
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
}
