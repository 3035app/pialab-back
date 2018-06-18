<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Structure;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use PiaApi\Entity\Pia\PiaTemplate;

class EditStructureTypeForm extends CreateStructureTypeForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('submit')

            ->add('templates', EntityType::class, [
                'class'        => PiaTemplate::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true,
                'by_reference' => false,
                'label'        => 'pia.structure_types.forms.edit.templates',
                'label_attr'   => [
                    'title' => 'pia.structure_types.forms.edit.templates_help',
                ],
            ])

            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;',
                ],
                'label' => 'pia.structure_types.forms.edit.cancel',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;',
                ],
                'label' => 'pia.structure_types.forms.edit.submit',
            ])
        ;
    }
}
