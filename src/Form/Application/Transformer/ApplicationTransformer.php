<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Application\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use PiaApi\Entity\Oauth\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ApplicationTransformer implements DataTransformerInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function transform($value)
    {
        if ($value instanceof Client) {
            return $value->getId();
        }

        return -1;
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        return $this->doctrine->getManager()->getRepository(Client::class)->find($value);
    }
}
