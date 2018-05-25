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
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $dpoOpinion = '';
    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $concernedPeopleSearchedOpinion;
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $concernedPeopleSearchedContent;
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $rejectionReason = '';
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $appliedAdjustements = '';

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="pia", cascade={"remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Answer[]
     */
    protected $answers;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="pia", cascade={"remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Comment[]
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Evaluation", mappedBy="pia", cascade={"remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Evaluation[]
     */
    protected $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="Measure", mappedBy="pia", cascade={"remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Measure[]
     */
    protected $measures;

    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="pia", cascade={"remove"})
     * @JMS\Exclude()
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
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $peopleNames = '';

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     *
     * @var bool
     */
    protected $isExample = false;

    /**
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="pias")
     * @JMS\Exclude()
     *
     * @var Structure
     */
    protected $structure;

    public function getAnswers()
    {
        return $this->answers->getValues();
    }

    /**
     * @return Structure
     */
    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     */
    public function setStructure(?Structure $structure): void
    {
        $this->structure = $structure;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("progress")
     *
     * @return int
     */
    public function computeProgress(): int
    {
        return round((100 / 36) * count($this->answers));
    }
}
