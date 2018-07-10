<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User\Transformer;

use PiaApi\Entity\Pia\Portfolio;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PortfoliosTransformer implements DataTransformerInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function transform($values)
    {
        if (null === $values) {
            return [];
        }
        $choices = [];
        foreach ($values as $v) {
            $choices[] = $v->getId();
        }

        return $choices;
    }

    public function reverseTransform($array)
    {
        if (null === $array) {
            return [];
        }
        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return $this->doctrine->getManager()->getRepository(Portfolio::class)->findBy(['id' => $array]);
    }
}
