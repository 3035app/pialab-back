<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerInterface;
use PiaApi\DataExchange\Validator\JsonValidator;
use PiaApi\Entity\Pia\Answer;
use PiaApi\Entity\Pia\Comment;
use PiaApi\Entity\Pia\Pia;
use PiaApi\Entity\Pia\Evaluation;
use PiaApi\Entity\Pia\Measure;
use PiaApi\Entity\Pia\Attachment;
use JMS\Serializer\SerializationContext;
use PiaApi\DataExchange\DataExchangeDescriptor;

class JsonToEntityTransformer
{
    /**
     * @var JsonValidator
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(JsonValidator $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Transforms a json representation of a Pia to Pia entity.
     *
     * @param string|array $jsonOrArray
     *
     * @return Pia
     */
    public function transform($jsonOrArray): Pia
    {
        if (is_array($jsonOrArray)) {
            $objectAsArray = $this->validator->validate($jsonOrArray);
        } else {
            $objectAsArray = $this->validator->parseAndValidate($jsonOrArray);
        }

        // dump($objectAsArray);

        // Creates the PIA

        /** @var Pia $pia */
        $pia = $this->serializer->fromArray($objectAsArray['pia'], Pia::class);

        // Add comments

        $pia->setComments(new ArrayCollection());

        foreach ($objectAsArray['comments'] as $comment) {
            /** @var Comment $piaComment */
            $piaComment = $this->serializer->fromArray($comment, Comment::class);
            $piaComment->setPia($pia);
            $pia->getComments()->add($piaComment);
        }

        // Add answers

        $pia->setAnswers(new ArrayCollection());

        foreach ($objectAsArray['answers'] as $answer) {
            /** @var Answer $piaAnswer */
            $piaAnswer = $this->serializer->fromArray($answer, Answer::class);
            $piaAnswer->setPia($pia);
            $pia->getAnswers()->add($piaAnswer);
        }

        // Add evaluations

        $pia->setEvaluations(new ArrayCollection());

        foreach ($objectAsArray['evaluations'] as $evaluation) {
            /** @var Evaluation $piaEvaluation */
            $piaEvaluation = $this->serializer->fromArray($evaluation, Evaluation::class);
            $piaEvaluation->setPia($pia);
            $pia->getEvaluations()->add($piaEvaluation);
        }

        // Add measures

        $pia->setMeasures(new ArrayCollection());

        foreach ($objectAsArray['measures'] as $measure) {
            /** @var Measure $piaMeasure */
            $piaMeasure = $this->serializer->fromArray($measure, Measure::class);
            $piaMeasure->setPia($pia);
            $pia->getMeasures()->add($piaMeasure);
        }

        // Add attachments

        $pia->setAttachments(new ArrayCollection());

        if (isset($objectAsArray['attachments'])) {
            foreach ($objectAsArray['attachments'] as $attachment) {
                /** @var Attachment $piaAttachment */
                $piaAttachment = $this->serializer->fromArray($attachment, Attachment::class);
                $piaAttachment->setPia($pia);
                $pia->getAttachments()->add($piaAttachment);
            }
        }

        $pia->setCreatedAt(new \DateTime());
        $pia->setUpdatedAt(new \DateTime());

        return $pia;
    }

    public function reverseTransform(Pia $pia): string
    {
        $exportObject = new DataExchangeDescriptor();

        /** @var SerializationContext $context */
        $context = SerializationContext::create();
        $context->setGroups(['Export']);

        // Trigger Doctrine lazy-load
        $pia->getAnswers()->count();
        $pia->getAttachments()->count();
        $pia->getComments()->count();
        $pia->getEvaluations()->count();
        $pia->getMeasures()->count();

        $exportObject->pia = $pia;
        $exportObject->answers = $pia->getAnswers();
        $exportObject->attachments = $pia->getAttachments();
        $exportObject->comments = $pia->getComments();
        $exportObject->evaluations = $pia->getEvaluations();
        $exportObject->measures = $pia->getMeasures();

        $serializedPia = $this->serializer->serialize($exportObject, 'json', $context);

        return $serializedPia;
    }

    public function entityToJson($object): string
    {
        $context = SerializationContext::create();
        $context->setGroups(['Export']);

        $serializedObject = $this->serializer->serialize($object, 'json', $context);

        return $serializedObject;
    }

}
