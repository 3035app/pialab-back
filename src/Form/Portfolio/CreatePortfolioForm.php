<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Portfolio;

use PiaApi\Form\BaseForm;
use PiaApi\Form\Structure\Type\StructureChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreatePortfolioForm extends BaseForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label'    => 'pia.portfolios.forms.create.name',
            ])
            ->add('structures', StructureChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'pia.portfolios.forms.create.structures',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.portfolios.forms.create.submit',
            ])
        ;
    }
}
