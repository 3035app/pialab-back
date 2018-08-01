<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
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
        //special case when logout
        if ($tokenStorage->getToken() !== null) {
            $this->user = $tokenStorage->getToken()->getUser();
        }
        $this->roleHierarchy = $roleHierarchy;
    }
    
    protected function getUserRoles(User $user): array
    {
        $userRoles = array_map(function ($roleName) {
            return new Role($roleName);
        }, $user->getRoles());
        
        return $userRoles;
    }

    protected function getReachableRoles(User $user): array
    {
        // Core roleHierarchy needs a Role array but we use string array
        $userRoles = $this->getUserRoles($user);
      
        $reachableRoleNames = array_map(function ($role) {
            return $role->getRole();
        }, $this->roleHierarchy->getReachableRoles($userRoles));
        
        return $reachableRoleNames;
    }

    protected function getAcessibleRoles(User $user): array
    {
        $reachableRoleNames = $this->getReachableRoles($user);
        
        return array_intersect($this->definedRoles, $reachableRoleNames);
    }

    public function getUserAccessibleRoles(): array
    {
        return $this->getAcessibleRoles($this->user);
    }

    public function isGranted(User $user, string $roleOrPermission)
    {
        $reachableRoleNames = $this->getReachableRoles($user);

        return in_array($roleOrPermission, $reachableRoleNames);
    }
    
    public function hasHigherRole(User $current_user, User $other_user)
    {
        $current_roles = $this->getAcessibleRoles($current_user);
        $other_roles = $this->getAcessibleRoles($other_user);
        
        return count($current_roles) >= count($other_roles);
    }
}
