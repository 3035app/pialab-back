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
use DateTime;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use PiaApi\Entity\Pia\Traits\HasPiaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="pia_evaluation")
 */
class Evaluation implements Timestampable
{
    use ResourceTrait,
        HasPiaTrait,
        TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity="Pia", inversedBy="evaluations")
     * @JMS\Exclude()
     *
     * @var Pia
     */
    protected $pia;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $status = 0;
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $referenceTo;
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $actionPlanComment;
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $evaluationComment;
    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $evaluationDate;
    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    protected $gauges;
    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $estimatedImplementationDate;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $personInCharge;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $globalStatus = 0;
}
