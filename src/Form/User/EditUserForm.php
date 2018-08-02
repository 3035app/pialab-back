<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use PiaApi\Form\User\Type\RolesType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PiaApi\Form\Portfolio\Type\PortfolioChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserForm extends CreateUserForm
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'hasPortfolio' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('roles')
            ->remove('password')
            ->remove('submit')
            ->remove('sendResettingEmail')

            ->add('username', TextType::class, [
                'label' => 'pia.users.forms.edit.username',
            ])
            ->add('profile', UserProfileForm::class, [
                'label' => false,
            ])
            ->add('roles', RolesType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label'    => 'pia.users.forms.edit.roles',
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'pia.users.forms.edit.expirationDate',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label'    => 'pia.users.forms.edit.enabled',
            ])
            ->add('locked', CheckboxType::class, [
                'required' => false,
                'label'    => 'pia.users.forms.edit.locked',
            ])
            ->add('portfolios', PortfolioChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'pia.users.forms.create.portfolios',
            ])
            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;',
                ],
                'label' => 'pia.users.forms.edit.cancel',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;',
                ],
                'label' => 'pia.users.forms.edit.submit',
            ]);

        if ($options['hasPortfolio'] === false) {
            $builder->remove('portfolios');
        }
    }
}
