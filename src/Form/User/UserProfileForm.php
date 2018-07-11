<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User;

use PiaApi\Form\BaseForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use PiaApi\Form\User\DataTransformer\UserProfileTransformer;

class UserProfileForm extends BaseForm
{
    /**
     * @var UserProfileTransformer
     */
    protected $profileTransformer;

    public function __construct(
        UserProfileTransformer $profileTransformer
    ) {
        $this->profileTransformer = $profileTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', HiddenType::class)

            ->add('lastName', TextType::class, [
                'required' => true,
                'label'    => 'pia.users.forms.profile.lastName',
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label'    => 'pia.users.forms.profile.firstName',
            ])
        ;
        $builder->addModelTransformer($this->profileTransformer);
    }
}
