<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use PiaApi\Form\User\Type\RolesType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PiaApi\Form\Structure\Type\StructureChoiceType;
use PiaApi\Form\Application\Type\ApplicationChoiceType;
use PiaApi\Form\DataTransformer\EntityDataTransformer;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Entity\Pia\Structure;

class CreateUserForm extends BaseForm
{
    /**
     * @var EntityDataTransformer
     */
    private $entityDataTransformer;

    public function __construct(EntityDataTransformer $entityDataTransformer)
    {
        $this->entityDataTransformer = $entityDataTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (!$options['application']) {
            $builder
                ->add('application', ApplicationChoiceType::class, [
                    'required' => true,
                    'label'    => 'pia.users.forms.create.application',
                ]);
        } else {
            $builder
                ->add('application', HiddenType::class, [
                    'required'   => true,
                    'data'       => $options['application'],
                    'data_class' => null,
                ]);

            $builder->get('application')->addModelTransformer($this->entityDataTransformer->forEntity(Client::class));
        }
        if (!$options['structure']) {
            $builder
                ->add('structure', StructureChoiceType::class, [
                    'required' => false,
                    'label'    => 'pia.users.forms.create.structure',
                ]);
        } else {
            $builder
                ->add('structure', HiddenType::class, [
                    'required'   => true,
                    'data'       => $options['structure'],
                    'data_class' => null,
                ]);

            $builder->get('structure')->addModelTransformer($this->entityDataTransformer->forEntity(Structure::class));
        }

        $builder
            ->add('profile', UserProfileForm::class, [
                'label'   => false,
            ])
            ->add('email', EmailType::class, [
                'label'    => 'pia.users.forms.create.email',
            ])
            ->add('password', PasswordType::class, [
                'label'    => 'pia.users.forms.create.password',
            ])
            ->add('roles', RolesType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,

                'label'    => 'pia.users.forms.create.roles',
            ])
            ->add('sendResettingEmail', CheckboxType::class, [
                'required'     => false,
                'label'        => 'pia.users.forms.create.sendResettingEmail',
                'label_attr'   => [
                    'title' => 'pia.users.forms.create.sendResettingEmail_help',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.users.forms.create.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'application' => false,
            'structure'   => false,
        ]);
    }
}
