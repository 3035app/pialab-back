<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
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
                'label'        => 'Gabarits de PIA disponibles',
                'label_attr'   => [
                    'title' => 'Les gabarits sélectionnés seront accessibles à toutes les structures de ce type de structure',
                ],
            ])

            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;',
                ],
                'label' => 'Annuler',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;',
                ],
                'label' => 'Enregistrer le type de structure',
            ])
        ;
    }
}
