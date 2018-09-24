<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Descriptor;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class PiaDescriptor extends AbstractDescriptor
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $status = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $authorName = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $evaluatorName = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $validatorName = '';

    /**
     * @JMS\Type("int")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var int
     */
    protected $dpoStatus = -1;

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $dpoOpinion = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $concernedPeopleOpinion = '';

    /**
     * @JMS\Type("int")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var int
     */
    protected $concernedPeopleStatus = '';

    /**
     * @JMS\Type("bool")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $concernedPeopleSearchedOpinion = false;

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $concernedPeopleSearchedContent = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $rejectionReason = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $appliedAdjustments = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $dposNames = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var string
     */
    protected $peopleNames = '';

    /**
     * @JMS\Type("bool")
     * @JMS\Groups({"Default", "Export"})
     *
     * @var bool
     */
    protected $isExample = false;

    /**
     * @JMS\Type("DateTime")
     * @JMS\Groups({"Export"})
     *
     * @var \DateTime|null
     */
    protected $createdAt = '';

    /**
     * @JMS\Type("DateTime")
     * @JMS\Groups({"Export"})
     *
     * @var \DateTime|null
     */
    protected $updatedAt = '';

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "Export"})
     * @Assert\NotBlank
     *
     * @var string
     */
    protected $type = '';

    /**
     * @JMS\Type("int")
     * @JMS\Groups({"Export"})
     *
     * @var int
     */
    protected $numberOfQuestions = 0;

    /**
     * @JMS\Type("int")
     * @JMS\Groups({"Export"})
     *
     * @var int
     */
    protected $progress = 0;

    /**
     * @JMS\Type("array")
     * @JMS\Groups({"Default", "Export"})
     */
    protected $answers = [];

    public function __construct(
        string $status,
        string $author,
        string $evaluator,
        string $validator,
        int $dpoStatus,
        string $dpoOpinion,
        string $concernedPeopleOpinion,
        int $concernedPeopleStatus,
        bool $concernedPeopleSearchedOpinion,
        string $concernedPeopleSearchedContent,
        string $rejectionReason,
        string $appliedAdjustments,
        string $dposNames,
        string $peopleNames,
        bool $isExample,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        string $type,
        int $numberOfQuestions,
        int $progress
    ) {
        $this->status = $status;
        $this->authorName = $author;
        $this->evaluatorName = $evaluator;
        $this->validatorName = $validator;
        $this->dpoStatus = $dpoStatus;
        $this->dpoOpinion = $dpoOpinion;
        $this->concernedPeopleOpinion = $concernedPeopleOpinion;
        $this->concernedPeopleStatus = $concernedPeopleStatus;
        $this->concernedPeopleSearchedOpinion = $concernedPeopleSearchedOpinion;
        $this->concernedPeopleSearchedContent = $concernedPeopleSearchedContent;
        $this->rejectionReason = $rejectionReason;
        $this->appliedAdjustments = $appliedAdjustments;
        $this->dposNames = $dposNames;
        $this->peopleNames = $peopleNames;
        $this->isExample = $isExample;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->type = $type;
        $this->numberOfQuestions = $numberOfQuestions;
        $this->progress = $progress;
    }

    public function mergeAnswers(array $answers)
    {
        $this->answers = array_merge($this->answers, $answers);
    }
}
