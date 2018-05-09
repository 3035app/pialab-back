<?php

namespace PiaApi\Form\Applications;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use OAuth2\OAuth2;

class CreateApplicationForm extends AbstractType
{
    protected $grantTypes = [
        OAuth2::GRANT_TYPE_AUTH_CODE          => OAuth2::GRANT_TYPE_AUTH_CODE,
        OAuth2::GRANT_TYPE_IMPLICIT           => OAuth2::GRANT_TYPE_IMPLICIT,
        OAuth2::GRANT_TYPE_USER_CREDENTIALS   => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
        OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
        OAuth2::GRANT_TYPE_REFRESH_TOKEN      => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
        OAuth2::GRANT_TYPE_EXTENSIONS         => OAuth2::GRANT_TYPE_EXTENSIONS,
    ];
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('allowedGrantTypes', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->grantTypes
            ])
            ->add('redirectUris', CollectionType::class, [
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'required' => false
                ],
                'allow_add'     => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'prototype'     => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid'
                ],
                'label' => 'Créer l\'application'
            ])
        ;
    }
}
