<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EditUserForm extends CreateUserForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('roles')
            ->remove('password')
            ->remove('submit')

            ->add('username', TextType::class, [
                'label'    => 'Nom d\'utilisateur',
            ])
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->userRoles,
                'label'    => 'Rôles',
            ])

            ->add('expirationDate', DateType::class, [
                'widget'   => 'single_text',
                'label'    => 'Date d\'expiration du compte',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label'    => 'Actif',
            ])
            ->add('locked', CheckboxType::class, [
                'required' => false,
                'label'    => 'Verrouillé',
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
                'label' => 'Enregistrer l\'utilisateur',
            ])
        ;
    }
}
