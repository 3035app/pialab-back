<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Validator;

use PiaApi\Entity\Pia\Pia;
use JMS\Serializer\SerializerBuilder;

class JsonValidator
{
    /**
     * Validates that the json is a valid representation of Pia end serve array representation.
     *
     * @param string $json
     *
     * @return array
     */
    public function parseAndValidate(string $json): array
    {
        try {
            $serializer = SerializerBuilder::create()->build();
            $objectAsArray = $serializer->deserialize($json, 'array', 'json');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Cannot deserialize json, reason : %s', $e->getMessage()));
        }

        $this->checkRootKeys($objectAsArray);

        return $objectAsArray;
    }

    /**
     * Check that mandatory keys are found in deserialized array.
     *
     * @param array $objectAsArray
     */
    private function checkRootKeys(array $objectAsArray)
    {
        if (count(array_diff_key($objectAsArray, $this->mandatoryKeys)) > 0) {
            $mandatoryKeys = array_keys($this->mandatoryKeys);
            $currentKeys = array_keys($objectAsArray);
            throw new \InvalidArgumentException(sprintf('Missing mandatory key, got %s, expected %s', implode(', ', $currentKeys), implode(', ', $mandatoryKeys)));
        }
    }
}
