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
use PiaApi\Entity\Traits\ResourceTrait;
use PiaApi\Entity\Traits\HasPiaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_comment")
 */
class Comment implements Timestampable
{
    use ResourceTrait,
        HasPiaTrait,
        TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Pia", inversedBy="comments")
     *
     * @var Pia
     */
    protected $pia;
    
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $description;
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $referenceTo;
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $forMeasure = false;
}
