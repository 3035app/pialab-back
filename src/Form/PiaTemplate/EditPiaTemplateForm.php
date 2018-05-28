<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\PiaTemplate;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditPiaTemplateForm extends CreatePiaTemplateForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('submit')
            ->remove('data')

            ->add('importedFileName', TextType::class, [
                'label'      => 'Nom du fichier actuel',
                'required'   => false,
                'disabled'   => true,
            ])

            ->add('newData', FileType::class, [
                'mapped'     => false,
                'label'      => 'Nouveau fichier d\'export (Laissez vide si vous n\'avez pas besoin de changer de fichier)',
                'required'   => false,
                'data_class' => null,
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
                'label' => 'Enregistrer le gabarit',
            ])
        ;
    }
}
