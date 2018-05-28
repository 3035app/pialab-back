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
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;
use PiaApi\Entity\Pia\Traits\ResourceTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Answer[]
     */
    protected $answers;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Comment[]
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Evaluation", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Evaluation[]
     */
    protected $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="Measure", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Exclude()
     *
     * @var Collection|Measure[]
     */
    protected $measures;

    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="pia", cascade={"persist","remove"})
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
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="pias").
     * @JMS\Exclude()
     *
     * @var Structure
     */
    protected $structure;

    /**
     * @ORM\ManyToOne(targetEntity="PiaTemplate", inversedBy="pias")
     *
     * @var PiaTemplate
     */
    protected $template;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->measures = new ArrayCollection();
        $this->attachments = new ArrayCollection();
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
     * @return PiaTemplate
     */
    public function getTemplate(): ?PiaTemplate
    {
        return $this->template;
    }

    /**
     * @param PiaTemplate $template
     */
    public function setTemplate(?PiaTemplate $template): void
    {
        $this->template = $template;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @param Collection|Answer[] $answers
     */
    public function setAnswers(Collection $answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Collection|Comment[] $comments
     */
    public function setComments(Collection $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    /**
     * @param Collection|Evaluation[] $evaluations
     */
    public function setEvaluations(Collection $evaluations): void
    {
        $this->evaluations = $evaluations;
    }

    /**
     * @return Collection|Measure[]
     */
    public function getMeasures(): Collection
    {
        return $this->measures;
    }

    /**
     * @param Collection|Measure[] $measures
     */
    public function setMeasures(Collection $measures): void
    {
        $this->measures = $measures;
    }

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * @param Collection|Attachment[] $attachments
     */
    public function setAttachments(Collection $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
