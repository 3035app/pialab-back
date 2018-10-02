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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProcessingTransformer extends AbstractTransformer
{
    /**
     * @var ProcessingService
     */
    protected $processingService;

    /**
     * @var Folder|null
     */
    protected $folder = null;

    /**
     * @var PiaTransformer
     */
    protected $piaTransformer;

    /**
     * @var DataTypeTransformer
     */
    protected $dataTypeTransformer;

    public function __construct(
        SerializerInterface $serializer,
        ProcessingService $processingService,
        ValidatorInterface $validator,
        PiaTransformer $piaTransformer,
        DataTypeTransformer $dataTypeTransformer
    ) {
        parent::__construct($serializer, $validator);

        $this->processingService = $processingService;
        $this->piaTransformer = $piaTransformer;
        $this->dataTypeTransformer = $dataTypeTransformer;
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
            $descriptor->getDesignatedController()
        );

        $processing->setDescription($descriptor->getDescription());
        $processing->setProcessors($descriptor->getProcessors());
        $processing->setNonEuTransfer($descriptor->getNonEuTransfer());
        $processing->setLifeCycle($descriptor->getLifeCycle());
        $processing->setStorage($descriptor->getStorage());
        $processing->setStandards($descriptor->getStandards());
        $processing->setStatus((int) $descriptor->getStatus());
        $processing->setLawfulness($descriptor->getLawfulness());
        $processing->setMinimization($descriptor->getMinimization());
        $processing->setRightsGuarantee($descriptor->getRightsGuarantee());
        $processing->setExactness($descriptor->getExactness());
        $processing->setConsent($descriptor->getConsent());
        $processing->setRecipients($descriptor->getRecipients());
        $processing->setContextOfImplementation($descriptor->getContextOfImplementation());

        return $processing;
    }

    public function fromProcessing(Processing $processing): ProcessingDescriptor
    {
        $descriptor = new ProcessingDescriptor(
            $processing->getName(),
            $processing->getAuthor(),
            $processing->getDesignatedController(),
            $processing->getControllers(),
            $processing->getDescription(),
            $processing->getProcessors(),
            $processing->getNonEuTransfer(),
            $processing->getLifeCycle(),
            $processing->getStorage(),
            $processing->getStandards(),
            $processing->getStatusName(),
            $processing->getLawfulness(),
            $processing->getMinimization(),
            $processing->getRightsGuarantee(),
            $processing->getExactness(),
            $processing->getConsent(),
            $processing->getContextOfImplementation(),
            $processing->getRecipients(),
            $processing->getCreatedAt(),
            $processing->getUpdatedAt()
        );

        $descriptor->mergePias(
            $this->piaTransformer->importPias($processing->getPias())
        );

        $descriptor->mergeDataTypes(
            $this->dataTypeTransformer->importDataTypes($processing->getProcessingDataTypes())
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

    public function extractDataType(Processing $processing, array $json)
    {
        $this->dataTypeTransformer->setProcessing($processing);

        return $this->dataTypeTransformer->jsonToDataType($json);
    }
}
