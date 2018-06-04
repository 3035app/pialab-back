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
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RolesType extends AbstractType
{
    private $roles = [];

    public function __construct(TokenStorageInterface $tokenStorage, array $roles, RoleHierarchyInterface $roleHierarchy)
    {
        $userRoleNames = $tokenStorage->getToken()->getUser()->getRoles();
        $userRoles = array_map(function ($roleName) {
            return new Role($roleName);
        }, $userRoleNames);

        $reachableRoleNames = array_map(function ($role) {
            return $role->getRole();
        }, $roleHierarchy->getReachableRoles($userRoles));

        $roleNames = array_filter(array_keys($roles), function ($roleName) use ($reachableRoleNames) {
            return in_array($roleName, $reachableRoleNames);
        });

        $this->roles = array_combine($roleNames, $roleNames);
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
