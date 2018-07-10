<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructureChoiceType extends AbstractType
{
    public function __construct()
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'multiple' => false,
            'expanded' => false,
            'choices'  => $this->getStructures(),
        ));
    }

    protected function getStructures()
    {
    }

    public function getParent()
    {
        return SearchChoiceType::class;
    }
}
