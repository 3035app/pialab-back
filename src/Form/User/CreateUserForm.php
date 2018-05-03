<?php

namespace PiaApi\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreateUserForm extends AbstractType
{
    protected $userRoles = [
        'ROLE_USER'        => 'ROLE_USER',
        'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->userRoles
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid'
                ],
                'label' => 'Créer l\'utilisateur'
            ])
        ;
    }
}
