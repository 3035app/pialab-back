<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\PiaTemplate;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditPiaTemplateForm extends CreatePiaTemplateForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('submit')
            ->remove('data')

            ->add('enabled', CheckboxType::class, [
                'required'   => false,
                'label'      => 'pia.templates.forms.edit.enabled',
                'label_attr' => [
                    'title' => 'pia.templates.forms.edit.enabled_help',
                ],
            ])

            ->add('importedFileName', TextType::class, [
                'label'      => 'pia.templates.forms.edit.importedFileName',
                'required'   => false,
                'disabled'   => true,
            ])

            ->add('newData', FileType::class, [
                'mapped'       => false,
                'required'     => false,
                'data_class'   => null,
                'label'        => 'pia.templates.forms.edit.newData',
                'label_attr'   => [
                    'title' => 'pia.templates.forms.edit.newData_help',
                ],
            ])

            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;',
                ],
                'label' => 'pia.templates.forms.edit.cancel',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;',
                ],
                'label' => 'pia.templates.forms.edit.submit',
            ])
        ;
    }
}
