<?php

/*
 * Copyright (C) 2015-2019 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use PiaApi\Entity\Pia\Pia;
use PiaApi\Entity\Pia\Processing;
use PiaApi\DataExchange\Descriptor\PiaDescriptor;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PiaTransformer extends AbstractTransformer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var PiaService
     */
    protected $piaService;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Processing|null
     */
    protected $processing = null;

    /**
     * @var AnswerTransformer
     */
    protected $answerTransformer;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        AnswerTransformer $answerTransformer
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->answerTransformer = $answerTransformer;
    }

    public function setProcessing(Processing $processing)
    {
        $this->processing = $processing;
    }

    public function getProcessing(): Processing
    {
        return $this->processing;
    }

    public function toPia(PiaDescriptor $descriptor): Pia
    {
        $pia = new Pia();
        $pia->setStatus($descriptor->getStatus());
        $pia->setAuthorName($descriptor->getAuthorName());
        $pia->setEvaluatorName($descriptor->getEvaluatorName());
        $pia->setValidatorName($descriptor->getValidatorName());
        $pia->setDpoStatus($descriptor->getDpoStatus());
        $pia->setDpoOpinion($descriptor->getDpoOpinion());
        $pia->setConcernedPeopleOpinion($descriptor->getConcernedPeopleOpinion());
        $pia->setConcernedPeopleStatus($descriptor->getConcernedPeopleStatus());
        $pia->setConcernedPeopleSearchedOpinion($descriptor->getConcernedPeopleSearchedOpinion());
        $pia->setConcernedPeopleSearchedContent($descriptor->getConcernedPeopleSearchedContent());
        $pia->setRejectionReason($descriptor->getRejectionReason());
        $pia->setAppliedAdjustments($descriptor->getAppliedAdjustments());
        $pia->setDposNames($descriptor->getDposNames());
        $pia->setPeopleNames($descriptor->getPeopleNames());
        $pia->setIsExample($descriptor->getIsExample());
        $pia->setType($descriptor->getType());

        $pia->setProcessing($this->processing);

        $pia->setAnswers(new ArrayCollection($descriptor->getAnswers()));

        return $pia;
    }

    public function fromPia(Pia $pia): PiaDescriptor
    {
        $descriptor = new PiaDescriptor(
            $pia->getStatusName(),
            $pia->getAuthorName(),
            $pia->getEvaluatorName(),
            $pia->getValidatorName(),
            $pia->getDpoStatus(),
            $pia->getDpoOpinion(),
            $pia->getConcernedPeopleOpinion(),
            $pia->getConcernedPeopleStatus(),
            $pia->getConcernedPeopleSearchedOpinion(),
            $pia->getConcernedPeopleSearchedContent(),
            $pia->getRejectionReason(),
            $pia->getAppliedAdjustments(),
            $pia->getDposNames(),
            $pia->getPeopeNames(),
            $pia->getIsExample(),
            $pia->getCreatedAt(),
            $pia->getUpdatedAt(),
            $pia->getType(),
            $pia->getNumberOfQuestions(),
            $pia->computeProgress()
        );

        $descriptor->mergeAnswers(
            $this->answerTransformer->importAnswers($pia->getAnswers()->getValues())
        );

        return $descriptor;
    }

    public function importPias(array $pias): array
    {
        $descriptors = [];

        foreach ($pias as $pia) {
            $descriptors[] = $this->fromPia($pia);
        }

        return $descriptors;
    }

    public function piaToJson(Pia $pia): string
    {
        $descriptor = $this->fromPia($pia);

        return $this->toJson($descriptor);
    }

    public function jsonToPia(array $json): Pia
    {
        $descriptor = $this->fromJson($json, PiaDescriptor::class);

        return $this->toPia($descriptor);
    }
}
