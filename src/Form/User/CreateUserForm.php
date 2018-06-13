<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Form\Application\Transformer\ApplicationTransformer;
use PiaApi\Form\Structure\Transformer\StructureTransformer;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Form\Type\RolesType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CreateUserForm extends BaseForm
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var ApplicationTransformer
     */
    protected $applicationTransformer;

    /**
     * @var StructureTransformer
     */
    protected $structureTransformer;

    /**
     * @var Security
     */
    protected $security;

    public function __construct(
      RegistryInterface $doctrine,
       ApplicationTransformer $applicationTransformer,
       StructureTransformer $structureTransformer)
    {
        $this->doctrine = $doctrine;
        $this->applicationTransformer = $applicationTransformer;
        $this->structureTransformer = $structureTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['application']) {
            $builder
            ->add('application', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => $this->getApplications($options),
                'label'    => 'pia.users.forms.create.application',
            ]);
        } else {
            $builder
                ->add('application', HiddenType::class, [
                    'required'   => true,
                    'data'       => $options['application'],
                    'data_class' => null,
                ]);
        }
        if (!$options['structure']) {
            $builder
            ->add('structure', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices'  => $this->getStructures($options),
                'label'    => 'pia.users.forms.create.structure',
            ]);
        } else {
            $builder
                  ->add('structure', HiddenType::class, [
                      'required'   => true,
                      'data'       => $options['structure'],
                      'data_class' => null,
                  ]);
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
            ->add('sendResetingEmail', CheckboxType::class, [
                'required'     => false,
                'label'        => 'pia.users.forms.create.sendResetingEmail',
                'label_attr'   => [
                    'title' => 'pia.users.forms.create.sendResetingEmail_help',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.users.forms.create.submit',
            ])
        ;

        $builder->get('application')->addModelTransformer($this->applicationTransformer);
        $builder->get('structure')->addModelTransformer($this->structureTransformer);
    }

    private function getApplications(array $options): array
    {
        $applications = [];
        if (!$options['application']) {
            foreach ($this->doctrine->getManager()->getRepository(Client::class)->findAll() as $application) {
                $applications[$application->getId()] = $application->getName() ?? $application->getId();
            }
        } else {
            $app = $options['application'];
            $applications[$app->getId()] = $app->getName();
        }

        return array_flip($applications);
    }

    private function getStructures(array $options): array
    {
        $structures = [];
        if (!$options['structure']) {
            foreach ($this->doctrine->getManager()->getRepository(Structure::class)->findAll() as $structure) {
                $structures[$structure->getId()] = $structure->getName() ?? $structure->getId();
            }
        } else {
            $struct = $options['structure'];
            $structures[$struct->getId()] = $struct->getName();
        }

        return array_flip($structures);
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
