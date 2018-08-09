<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use JMS\Serializer\SerializationContext;
use PiaApi\DataExchange\Descriptor\AbstractDescriptor;
use PiaApi\Exception\DataImportException;

class AbstractTransformer
{
    protected function validate(AbstractDescriptor $descriptor): bool
    {
        $errors = $this->validator->validate($descriptor);

        if ($errors->count() > 0) {
            throw new DataImportException(serialize($errors));
        }

        return true;
    }

    public function toJson(AbstractDescriptor $descriptor): string
    {
        $context = SerializationContext::create();
        $context->setGroups(['Export']);
        $context->setSerializeNull(true);

        return $this->serializer->serialize($descriptor, 'json', $context);
    }

    public function fromJson(array $json, string $class): AbstractDescriptor
    {
        $descriptor = $this->serializer->fromArray($json, $class);

        $this->validate($descriptor);

        return $descriptor;
    }
}
