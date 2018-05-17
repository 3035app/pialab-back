<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Application;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
            ])
            ->add('allowedGrantTypes', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->grantTypes,
                'label'    => 'Types d\'autorisations',
            ])
            ->add('redirectUris', CollectionType::class, [
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'required' => false,
                ],
                'allow_add'     => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'prototype'     => true,
                'label'         => 'URIs de redirection',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'Créer l\'application',
            ])
        ;
    }
}
