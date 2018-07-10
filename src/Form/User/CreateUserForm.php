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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Form\Application\Transformer\ApplicationTransformer;
use PiaApi\Form\Structure\Transformer\StructureTransformer;
use PiaApi\Form\User\Transformer\PortfoliosTransformer;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Form\Type\RolesType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PiaApi\Form\Type\SearchChoiceType;

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
     * @var PortfoliosTransformer
     */
    protected $portfolioTransformer;

    /**
     * @var Security
     */
    protected $security;

    public function __construct(
      RegistryInterface $doctrine,
       ApplicationTransformer $applicationTransformer,
       StructureTransformer $structureTransformer,
       PortfoliosTransformer $portfoliosTransformer)
    {
        $this->doctrine = $doctrine;
        $this->applicationTransformer = $applicationTransformer;
        $this->structureTransformer = $structureTransformer;
        $this->portfoliosTransformer = $portfoliosTransformer;
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
            ->add('portfolios', SearchChoiceType::class, [
                'label'    => 'pia.users.forms.create.portfolios',
                'multiple' => true,
                'choices'  => $this->getPortfolios(),
            ]);
        $builder->get('portfolios')->addModelTransformer($this->portfoliosTransformer);

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

    private function getPortfolios(): array
    {
        $portfolios = [];

        foreach ($this->doctrine->getManager()->getRepository(Portfolio::class)->findAll() as $portfolio) {
            $portfolios[$portfolio->getId()] = $portfolio->getName() ?? $portfolio->getId();
        }

        return array_flip($portfolios);
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
