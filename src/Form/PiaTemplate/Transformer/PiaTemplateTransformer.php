<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\PiaTemplate\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Pia\PiaTemplate;

class PiaTemplateTransformer implements DataTransformerInterface
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
        if ($value instanceof PiaTemplate) {
            return $value->getId();
        }

        return -1;
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        return $this->doctrine->getManager()->getRepository(PiaTemplate::class)->find($value);
    }
}
