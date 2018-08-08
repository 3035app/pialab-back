<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\Folder;
use PiaApi\DataExchange\Descriptor\ProcessingDescriptor;
use PiaApi\Services\ProcessingService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PiaApi\Exception\DataImportException;

class ProcessingTransformer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ProcessingService
     */
    protected $processingService;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Folder|null
     */
    protected $folder = null;

    public function __construct(
        SerializerInterface $serializer,
        ProcessingService $processingService,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->processingService = $processingService;
        $this->validator = $validator;
    }

    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;
    }

    public function getFolder(): Folder
    {
        return $this->folder;
    }

    public function toJson(ProcessingDescriptor $descriptor): string
    {
        $context = SerializationContext::create();
        $context->setGroups(['Export']);
        $context->setSerializeNull(true);

        return $this->serializer->serialize($descriptor, 'json', $context);
    }

    public function toProcessing(ProcessingDescriptor $descriptor): Processing
    {
        $processing = $this->processingService->createProcessing(
            $descriptor->getName(),
            $this->getFolder(),
            $descriptor->getAuthor(),
            $descriptor->getControllers()
        );

        $processing->setDescription($descriptor->getDescription());
        $processing->setProcessors($descriptor->getProcessors());
        $processing->setNonEuTransfer($descriptor->getNonEuTransfer());
        $processing->setLifeCycle($descriptor->getLifeCycle());
        $processing->setStorage($descriptor->getStorage());
        $processing->setStandards($descriptor->getStandards());
        $processing->setStatus(Processing::getStatusFromName($descriptor->getStatus()));

        return $processing;
    }

    public function fromJson(array $json): ProcessingDescriptor
    {
        $descriptor = $this->serializer->fromArray($json, ProcessingDescriptor::class);

        return $descriptor;
    }

    public function fromProcessing(Processing $processing): ProcessingDescriptor
    {
        $descriptor = new ProcessingDescriptor(
            $processing->getName(),
            $processing->getAuthor(),
            $processing->getControllers(),
            $processing->getDescription(),
            $processing->getProcessors(),
            $processing->getNonEuTransfer(),
            $processing->getLifeCycle(),
            $processing->getStorage(),
            $processing->getStandards(),
            $processing->getStatusName(),
            $processing->getCreatedAt(),
            $processing->getUpdatedAt()
        );

        return $descriptor;
    }

    public function processingToJson(Processing $processing): string
    {
        $descriptor = $this->fromProcessing($processing);

        return $this->toJson($descriptor);
    }

    public function jsonToProcessing(array $json): Processing
    {
        $descriptor = $this->fromJson($json);

        $errors = $this->validator->validate($descriptor);

        if ($errors->count() > 0) {
            throw new DataImportException(serialize($errors));
        }

        return $this->toProcessing($descriptor);
    }
}
