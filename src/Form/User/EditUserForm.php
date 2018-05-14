<?php

namespace PiaApi\Form\User;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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

            ->add('username', TextType::class)
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->userRoles
            ])

            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('locked', CheckboxType::class, [
                'required' => false
            ])
            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'red cancel',
                    'style' => 'width: 48%;float:right;'
                ],
                'label' => 'Annuler'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => '',
                    'style' => 'width: 48%;'
                ],
                'label' => 'Enregistrer l\'utilisateur'
            ])
        ;
    }
}
