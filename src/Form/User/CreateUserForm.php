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
use PiaApi\Form\User\Transformer\RolesTransformer;
use PiaApi\Entity\Pia\Structure;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @var RolesTransformer
     */
    protected $rolesTransformer;

    public function __construct(RegistryInterface $doctrine, ApplicationTransformer $applicationTransformer, StructureTransformer $structureTransformer, RolesTransformer $rolesTransformer)
    {
        $this->doctrine = $doctrine;
        $this->applicationTransformer = $applicationTransformer;
        $this->structureTransformer = $structureTransformer;
        $this->rolesTransformer = $rolesTransformer;
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
                'required'   => true,
                'multiple'   => false,
                'expanded'   => true,
                'choices'    => $this->rolesTransformer->getRolesForChoiceList(),
                'label'      => 'Rôles',
                'label_attr' => [
                    'title'  => 'Seulement 1 rôle peut être affecté à un utilisateur.',
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
        $builder->get('roles')->addModelTransformer($this->rolesTransformer);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();

            if (!$user instanceof UserInterface) {
                return;
            }
        });
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
