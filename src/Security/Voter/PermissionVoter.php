<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class PermissionVoter extends RoleHierarchyVoter
{
    public function __construct(RoleHierarchyInterface $roleHierarchy, string $prefix = 'CAN_')
    {
        parent::__construct($roleHierarchy, $prefix);
    }
}
