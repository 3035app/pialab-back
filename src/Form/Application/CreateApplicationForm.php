<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Application;

use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use OAuth2\OAuth2;

class CreateApplicationForm extends BaseForm
{
    protected $grantTypes = [
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_AUTH_CODE          => OAuth2::GRANT_TYPE_AUTH_CODE,
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_IMPLICIT           => OAuth2::GRANT_TYPE_IMPLICIT,
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_USER_CREDENTIALS   => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_REFRESH_TOKEN      => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
        'pia.applications.labels.grant_types.' . OAuth2::GRANT_TYPE_EXTENSIONS         => OAuth2::GRANT_TYPE_EXTENSIONS,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'pia.applications.forms.create.name',
            ])
            ->add('url', UrlType::class, [
                'label' => 'pia.applications.forms.create.url',
            ])
            ->add('allowedGrantTypes', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->grantTypes,
                'label'    => 'pia.applications.forms.create.allowedGrantTypes',
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
                'label'         => 'pia.applications.forms.create.redirectUris',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'fluid',
                ],
                'label' => 'pia.applications.forms.create.submit',
            ])
        ;
    }
}
