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
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\DataExchange\Descriptor\DataTypeDescriptor;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataTypeTransformer extends AbstractTransformer
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
     * @var Processing|null
     */
    protected $processing = null;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function setProcessing(Processing $processing)
    {
        $this->processing = $processing;
    }

    public function getProcessing(): Processing
    {
        return $this->processing;
    }

    public function toDataType(DataTypeDescriptor $descriptor): ProcessingDataType
    {
        $type = new ProcessingDataType($this->processing, $descriptor->getReference());

        $type->setData($descriptor->getData());
        $type->setSensitive($descriptor->getSensitive());

        return $type;
    }

    public function fromDataType(ProcessingDataType $type): DataTypeDescriptor
    {
        $descriptor = new DataTypeDescriptor(
            $type->getReference(),
            $type->getData(),
            $type->getRetentionPeriod(),
            $type->isSensitive()
        );

        return $descriptor;
    }

    public function importDataTypes(array $types): array
    {
        $descriptors = [];

        foreach ($types as $type) {
            $descriptors[] = $this->fromDataType($type);
        }

        return $descriptors;
    }

    public function dataTypeToJson(ProcessingDataType $type): string
    {
        $descriptor = $this->fromDataType($type);

        return $this->toJson($descriptor);
    }

    public function jsonToDataType(array $json): ProcessingDataType
    {
        $descriptor = $this->fromJson($json, DataTypeDescriptor::class);

        return $this->toDataType($descriptor);
    }
}
