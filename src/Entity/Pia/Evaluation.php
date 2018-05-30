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
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $status = 0;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $referenceTo;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $actionPlanComment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $evaluationComment;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var DateTime
     */
    protected $evaluationDate;

    /**
     * @ORM\Column(type="json")
     * @JMS\Type("array")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var array
     */
    protected $gauges;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var DateTime
     */
    protected $estimatedImplementationDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $personInCharge;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $globalStatus = 0;
}
