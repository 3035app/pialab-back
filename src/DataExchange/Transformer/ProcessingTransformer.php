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
use PiaApi\Entity\Pia\ProcessingStatus;
use PiaApi\Entity\Pia\Folder;
use PiaApi\DataExchange\Descriptor\ProcessingDescriptor;
use PiaApi\Services\ProcessingService;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProcessingTransformer extends AbstractTransformer
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

    /**
     * @var PiaTransformer
     */
    protected $piaTransformer;

    public function __construct(
        SerializerInterface $serializer,
        ProcessingService $processingService,
        ValidatorInterface $validator,
        PiaTransformer $piaTransformer
    ) {
        $this->serializer = $serializer;
        $this->processingService = $processingService;
        $this->validator = $validator;
        $this->piaTransformer = $piaTransformer;
    }

    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;
    }

    public function getFolder(): Folder
    {
        return $this->folder;
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
        $processing->setStatus(ProcessingStatus::getStatusFromName($descriptor->getStatus()));

        return $processing;
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

        $descriptor->mergePias(
            $this->piaTransformer->importPias($processing->getPias())
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
        $descriptor = $this->fromJson($json, ProcessingDescriptor::class);

        return $this->toProcessing($descriptor);
    }

    public function extractPia(Processing $processing, array $json)
    {
        $this->piaTransformer->setProcessing($processing);

        return $this->piaTransformer->jsonToPia($json);
    }
}
