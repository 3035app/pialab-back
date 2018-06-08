<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Form\Application\Transformer\ApplicationTransformer;
use PiaApi\Form\Structure\Transformer\StructureTransformer;
use PiaApi\Entity\Pia\Structure;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CreateUserForm extends AbstractType
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

    protected $userRoles = [
        'ROLE_USER'        => 'ROLE_USER',
        'ROLE_ADMIN'       => 'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
        'DPO'              => 'ROLE_DPO',
        'Data controller'  => 'ROLE_CONTROLLER',
    ];

    public function __construct(RegistryInterface $doctrine, ApplicationTransformer $applicationTransformer, StructureTransformer $structureTransformer)
    {
        $this->doctrine = $doctrine;
        $this->applicationTransformer = $applicationTransformer;
        $this->structureTransformer = $structureTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('application', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => $this->getApplications(),
                'label'    => 'Application',
            ])
            ->add('structure', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices'  => $this->getStructures(),
                'label'    => 'Structure',
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Adresse email',
            ])
            ->add('password', PasswordType::class, [
                'label'    => 'Mot de passe',
            ])
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->userRoles,
                'label'    => 'Rôles',
            ])
            ->add('sendResetingEmail', CheckboxType::class, [
                'required'     => false,
                'label'        => 'Envoyer l\'email de RàZ de mot de passe ?',
                'label_attr'   => [
                    'title' => 'Envoyer automatiquement l\'email de réinitialisation de mot de passe à l\'adresse email saisie',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'Créer l\'utilisateur',
            ])
        ;

        $builder->get('application')->addModelTransformer($this->applicationTransformer);
        $builder->get('structure')->addModelTransformer($this->structureTransformer);
    }

    private function getApplications(): array
    {
        $applications = [];

        foreach ($this->doctrine->getManager()->getRepository(Client::class)->findAll() as $application) {
            $applications[$application->getId()] = $application->getName() ?? $application->getId();
        }

        return array_flip($applications);
    }

    private function getStructures(): array
    {
        $structures = [];

        foreach ($this->doctrine->getManager()->getRepository(Structure::class)->findAll() as $structure) {
            $structures[$structure->getId()] = $structure->getName() ?? $structure->getId();
        }

        return array_flip($structures);
    }
}
