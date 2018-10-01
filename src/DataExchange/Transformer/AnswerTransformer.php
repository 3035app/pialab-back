<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use PiaApi\Entity\Pia\Answer;
use PiaApi\Entity\Pia\Pia;
use PiaApi\DataExchange\Descriptor\AnswerDescriptor;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnswerTransformer extends AbstractTransformer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Pia|null
     */
    protected $pia = null;

    /**
     * @var AnswerTransformer
     */
    protected $answerTransformer;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function setPia(Pia $pia)
    {
        $this->pia = $pia;
    }

    public function getPia(): Pia
    {
        return $this->pia;
    }

    public function toAnswer(AnswerDescriptor $descriptor): Answer
    {
        $answer = new Answer();

        $answer->setPia($descriptor->getPia());
        $answer->setReferenceTo($descriptor->getReferenceTo());
        $answer->setData($descriptor->getData());

        return $answer;
    }

    public function fromAnswer(Answer $answer): AnswerDescriptor
    {
        $descriptor = new AnswerDescriptor(
            $answer->getReferenceTo(),
            $answer->getData(),
            $answer->getCreatedAt(),
            $answer->getUpdatedAt()
        );

        return $descriptor;
    }

    public function importAnswers(array $answers): array
    {
        $descriptors = [];

        foreach ($answers as $answer) {
            $descriptors[] = $this->fromAnswer($answer);
        }

        return $descriptors;
    }

    public function answerToJson(Answer $answer): string
    {
        $descriptor = $this->fromAnswer($answer);

        return $this->toJson($descriptor);
    }

    public function jsonToAnswer(array $json): Answer
    {
        $descriptor = $this->fromJson($json, AnswerDescriptor::class);

        return $this->toAnswer($descriptor);
    }
}
