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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserProfileForm extends AbstractType
{
    protected $userRoles = [
        'DPO'             => 'ROLE_DPO',
        'Data controller' => 'ROLE_CONTROLLER',
        'Admin'          => 'ROLE_ADMIN'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', HiddenType::class)
            ->add('name', TextType::class, [
                'required' => true,
                'label'    => 'Name',
            ])
            ->add('piaRoles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => $this->userRoles,
                'label'    => 'Rôles Pialab',
            ])
        ;
    }
}
