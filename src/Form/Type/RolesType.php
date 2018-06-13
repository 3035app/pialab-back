<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PiaApi\Security\Role\RoleHierarchy;

class RolesType extends AbstractType
{
    private $roles = [];

    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $roleNames = $roleHierarchy->getUserAccessibleRoles();
        $roleLabels = array_map(function ($roleName) {
            return 'pia.users.labels.roles.' . $roleName;
        }, $roleNames);
        $this->roles = array_combine($roleLabels, $roleNames);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'required' => false,
          'multiple' => true,
          'expanded' => true,
          'choices'  => $this->roles,
          'label'    => 'Rôles',
    ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
