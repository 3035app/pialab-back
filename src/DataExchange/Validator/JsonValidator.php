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
use PiaApi\DataExchange\DataExchangeDescriptor;

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
        $this->removeDates($objectAsArray);
        $this->removeIDs($objectAsArray);

        return $objectAsArray;
    }

    /**
     * Check that mandatory keys are found in deserialized array.
     *
     * @param array $objectAsArray
     */
    private function checkRootKeys(array $objectAsArray)
    {
        if (count(array_diff_key($objectAsArray, DataExchangeDescriptor::STRUCTURE)) > 0) {
            $mandatoryKeys = array_keys(DataExchangeDescriptor::STRUCTURE);
            $currentKeys = array_keys($objectAsArray);
            throw new \InvalidArgumentException(sprintf('Missing mandatory key, got %s, expected %s', implode(', ', $currentKeys), implode(', ', $mandatoryKeys)));
        }
    }

    /**
     * Removes udpated_at, created_at ans *_date fields.
     *
     * @param array $data
     */
    private function removeDates(array &$data)
    {
        array_walk_recursive($data, function (&$element, $key) {
            if ($key === 'created_at' || $key === 'updated_at' || preg_match('/.*_date$/', $key)) {
                $dateObject = new \DateTime($element);
                $element = $dateObject->format(\DateTime::ISO8601);
            }
        });
    }

    /**
     * Removes exported IDs.
     *
     * @param array $data
     */
    private function removeIDs(array &$data)
    {
        array_walk_recursive($data, function (&$element, $key) {
            if ($key == 'id' || substr($key, -3) == '_id') {
                $element = null;
            }
        });
    }
}
