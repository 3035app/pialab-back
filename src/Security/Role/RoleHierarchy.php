<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use PiaApi\Entity\Oauth\User;
use Symfony\Component\Security\Core\Role\Role;

class RoleHierarchy
{
    private $rawRoles = [];
    private $definedRoles = [];
    /**
     * @var User
     */
    private $user;
    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    public function __construct(TokenStorageInterface $tokenStorage, array $roles, RoleHierarchyInterface $roleHierarchy)
    {
        $this->rawRoles = $roles;

        $this->definedRoles = array_filter(array_keys($roles), function ($role) {
            return substr($role, 0, 5) == 'ROLE_';
        });
        $this->user = $tokenStorage->getToken()->getUser();
        $this->roleHierarchy = $roleHierarchy;
    }

    public function getUserAccessibleRoles(): array
    {
        $userRoleNames = $this->user->getRoles();

        $userRoles = array_map(function ($roleName) {
            return new Role($roleName);
        }, $userRoleNames);

        $reachableRoleNames = array_map(function ($role) {
            return $role->getRole();
        }, $this->roleHierarchy->getReachableRoles($userRoles));

        $roleNames = array_filter($this->definedRoles, function ($roleName) use ($reachableRoleNames) {
            return in_array($roleName, $reachableRoleNames);
        });

        return $roleNames;
    }

    public function isGranted(User $user, string $roleOrPermission)
    {
        $userRoles = array_map(function ($roleName) {
            return new Role($roleName);
        }, $user->getRoles());

        $reachableRoleNames = array_map(function ($role) {
            return $role->getRole();
        }, $this->roleHierarchy->getReachableRoles($userRoles));

        return in_array($roleOrPermission, $reachableRoleNames);
    }
}
