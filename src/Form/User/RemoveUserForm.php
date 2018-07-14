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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class RemoveUserForm extends BaseForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('submit', ButtonType::class, [
                'attr' => [
                    'class' => 'ui fluid red button',
                ],
                'label' => 'pia.users.forms.remove.submit',
            ])
        ;
    }
}
