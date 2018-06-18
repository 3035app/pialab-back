<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Form\Application\Transformer\ApplicationTransformer;
use PiaApi\Form\Structure\Transformer\StructureTransformer;
use PiaApi\Form\User\Transformer\UserProfileTransformer;
use PiaApi\Form\Type\RolesType;

class EditUserForm extends CreateUserForm
{
    /**
     * @var UserProfileTransformer
     */
    protected $profileTransformer;

    public function __construct(
      RegistryInterface $doctrine,
        UserProfileTransformer $profileTransformer,
        ApplicationTransformer $applicationTransformer,
        StructureTransformer $structureTransformer
    ) {
        parent::__construct($doctrine, $applicationTransformer, $structureTransformer);
        $this->profileTransformer = $profileTransformer;
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
                'label'    => 'pia.users.forms.edit.username',
            ])
            ->add('profile', UserProfileForm::class, [
                'label'   => false,
            ])
            ->add('roles', RolesType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label'    => 'pia.users.forms.edit.roles',
            ])
            ->add('expirationDate', DateType::class, [
                'widget'   => 'single_text',
                'label'    => 'pia.users.forms.edit.expirationDate',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label'    => 'pia.users.forms.edit.enabled',
            ])
            ->add('locked', CheckboxType::class, [
                'required' => false,
                'label'    => 'pia.users.forms.edit.locked',
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

        $builder->get('profile')->addModelTransformer($this->profileTransformer);
    }
}
