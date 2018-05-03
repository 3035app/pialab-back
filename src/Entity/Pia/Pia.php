<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia\Pia;

use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
/**
 * @ORM\Entity
 * @ORM\Table(name="pia")
 */
class Pia implements Timestampable
{
    use ResourceTrait,
        TimestampableEntity;

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
    protected $name;
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $authorName = '';
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $evaluatorName = '';
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $validatorName = '';
    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $dpoStatus = 0;
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $dpoOpinion = '';
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $concernedPeopleOpinion = '';
    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    protected $concernedPeopleStatus = 0;
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $rejectionReason = '';
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $appliedAdjustements = '';

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="pia", cascade={"remove"})
     *
     * @var Collection|Answer[]
     */
    protected $answers;
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="pia", cascade={"remove"})
     *
     * @var Collection|Comment[]
     */
    protected $comments;
    /**
     * @ORM\OneToMany(targetEntity="Evaluation", mappedBy="pia", cascade={"remove"})
     *
     * @var Collection|Evaluation[]
     */
    protected $evaluations;
    /**
     * @ORM\OneToMany(targetEntity="Measure", mappedBy="pia", cascade={"remove"})
     *
     * @var Collection|Measure[]
     */
    protected $measures;
    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="pia", cascade={"remove"})
     *
     * @var Collection|Attachment[]
     */
    protected $attachments;
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $dposNames = '';
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $peopleNames = '';
    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @var bool
     */
    protected $isExample = false;

    public function getAnswers()
    {
        return $this->answers->getValues();
    }
}
