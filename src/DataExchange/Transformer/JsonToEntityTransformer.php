<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Transformer;

use PiaApi\Entity\Pia\Pia;
use PiaApi\DataExchange\Validator\JsonValidator;

class JsonToEntityTransformer
{
    /**
     * @var JsonValidator
     */
    protected $validator;

    public function __construct(JsonValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Transforms a json representation of a Pia to Pia entity.
     *
     * @param string $json
     *
     * @return Pia
     */
    public function transform(string $json): Pia
    {
        $objectAsArray = $this->validator->parseAndValidate($json);

        return new Pia();
    }
}
