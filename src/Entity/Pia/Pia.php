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

    const TYPE_REGULAR = 'regular';
    const TYPE_SIMPLIFIED = 'simplified';
    const TYPE_ADVANCED = 'advanced';

    protected const QUESTIONS = [
        self::TYPE_SIMPLIFIED => 6,
        self::TYPE_REGULAR    => 18,
        self::TYPE_ADVANCED   => 36,
    ];

    const QUESTION_NUMBER = 30;
    const OLD_QUESTION_NUMBER = 36;

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
    protected $authorName = '';

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $evaluatorName = '';

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $validatorName = '';

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $dpoStatus = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $dpoOpinion = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $concernedPeopleOpinion = '';

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var int
     */
    protected $concernedPeopleStatus = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $concernedPeopleSearchedOpinion;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $concernedPeopleSearchedContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $rejectionReason = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $appliedAdjustments = '';

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $dposNames = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $peopleNames = '';

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $isExample = false;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Groups({"Full", "Export"})
     *
     * @var Collection|Answer[]
     */
    protected $answers;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Groups({"Full", "Export"})
     *
     * @var Collection|Comment[]
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Evaluation", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Groups({"Full", "Export"})
     *
     * @var Collection|Evaluation[]
     */
    protected $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="Measure", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Groups({"Full", "Export"})
     *
     * @var Collection|Measure[]
     */
    protected $measures;

    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="pia", cascade={"persist","remove"})
     * @JMS\Groups({"Full", "Export"})
     *
     * @var Collection|Attachment[]
     */
    protected $attachments;

    /**
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="pias").
     * @JMS\Groups({"Full"})
     *
     * @var Structure
     */
    protected $structure;

    /**
     * @ORM\Column(type="string")
     * @JMS\Groups({"Default", "Full"})
     *
     * @var string
     */
    protected $type = self::TYPE_ADVANCED;

    /**
     * @ORM\ManyToOne(targetEntity="Processing", inversedBy="pias")
     * @JMS\Groups({"Default", "Full"})
     * @JMS\MaxDepth(1)
     *
     * @var Processing
     */
    protected $processing;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->measures = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getNumberOfQuestions(): int
    {
        $number = 0;

        if (array_key_exists($this->type, self::QUESTIONS)) {
            $number = self::QUESTIONS[$this->type];
        }

        return $number;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("progress")
     * @JMS\Groups({"Default", "Export"})
     *
     * @return int
     */
    public function computeProgress(): int
    {
        $currentAnswerCount = count($this->answers ?? []);
        $currentQuestionCount = self::QUESTION_NUMBER;

        if ($currentAnswerCount > $currentQuestionCount) {
            $currentQuestionCount = self::OLD_QUESTION_NUMBER;
        }

        $progress = round((100 / $currentQuestionCount) * $currentAnswerCount);

        return $progress;
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
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return string
     */
    public function getEvaluatorName(): string
    {
        return $this->evaluatorName;
    }

    /**
     * @param string $evaluatorName
     */
    public function setEvaluatorName(string $evaluatorName): void
    {
        $this->evaluatorName = $evaluatorName;
    }

    /**
     * @return string
     */
    public function getValidatorName(): string
    {
        return $this->validatorName;
    }

    /**
     * @param string $validatorName
     */
    public function setValidatorName(string $validatorName): void
    {
        $this->validatorName = $validatorName;
    }

    /**
     * @return int
     */
    public function getDpoStatus(): int
    {
        return $this->dpoStatus;
    }

    /**
     * @param int $dpoStatus
     */
    public function setDpoStatus(?int $dpoStatus): void
    {
        $this->dpoStatus = $dpoStatus;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return PiaStatus::getStatusName($this->status);
    }

    /**
     * @param int $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getConcernedPeopleStatus(): int
    {
        return $this->concernedPeopleStatus;
    }

    /**
     * @param int $concernedPeopleStatus
     */
    public function setConcernedPeopleStatus(int $concernedPeopleStatus): void
    {
        $this->concernedPeopleStatus = $concernedPeopleStatus;
    }

    /**
     * @return string|null
     */
    public function getDpoOpinion(): ?string
    {
        return $this->dpoOpinion;
    }

    /**
     * @param string $dpoOpinion
     */
    public function setDpoOpinion(?string $dpoOpinion): void
    {
        $this->dpoOpinion = $dpoOpinion;
    }

    /**
     * @return string|null
     */
    public function getConcernedPeopleOpinion(): ?string
    {
        return $this->concernedPeopleOpinion;
    }

    /**
     * @param string $concernedPeopleOpinion
     */
    public function setConcernedPeopleOpinion(?string $concernedPeopleOpinion): void
    {
        $this->concernedPeopleOpinion = $concernedPeopleOpinion;
    }

    /**
     * @return string|null
     */
    public function getConcernedPeopleSearchedContent(): ?string
    {
        return $this->concernedPeopleSearchedContent;
    }

    /**
     * @param string $concernedPeopleSearchedContent
     */
    public function setConcernedPeopleSearchedContent(?string $concernedPeopleSearchedContent): void
    {
        $this->concernedPeopleSearchedContent = $concernedPeopleSearchedContent;
    }

    /**
     * @return string|null
     */
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    /**
     * @param string $rejectionReason
     */
    public function setRejectionReason(?string $rejectionReason): void
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * @return string|null
     */
    public function getAppliedAdjustments(): ?string
    {
        return $this->appliedAdjustments;
    }

    /**
     * @param string $appliedAdjustments
     */
    public function setAppliedAdjustments(?string $appliedAdjustments): void
    {
        $this->appliedAdjustments = $appliedAdjustments;
    }

    /**
     * @return string
     */
    public function getDposNames(): string
    {
        return $this->dposNames;
    }

    /**
     * @param string $dposNames
     */
    public function setDposNames(?string $dposNames): void
    {
        $this->dposNames = $dposNames;
    }

    /**
     * @return string
     */
    public function getPeopeNames(): string
    {
        return $this->peopleNames;
    }

    /**
     * @param string $peopleNames
     */
    public function setPeopleNames(?string $peopleNames): void
    {
        $this->peopleNames = $peopleNames;
    }

    /**
     * @return bool
     */
    public function getConcernedPeopleSearchedOpinion(): bool
    {
        return $this->concernedPeopleSearchedOpinion;
    }

    /**
     * @param bool $concernedPeopleSearchedOpinion
     */
    public function setConcernedPeopleSearchedOpinion(?bool $concernedPeopleSearchedOpinion): void
    {
        $this->concernedPeopleSearchedOpinion = $concernedPeopleSearchedOpinion;
    }

    /**
     * @return bool
     */
    public function getIsExample(): bool
    {
        return $this->isExample;
    }

    /**
     * @param bool
     */
    public function setIsExample(bool $example)
    {
        $this->isExample = $example;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
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
