<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class RolesTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $roles;

    public function __construct(array $SecurityRoleHierarchyRoles)
    {
        $this->roles = $SecurityRoleHierarchyRoles;
    }

    /**
     * @param array $profile
     *
     * @return string
     */
    public function transform($roles)
    {
        if (is_array($roles)) {
            return array_values($roles)[0];
        }

        return $roles;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        return [$value];
    }

    /**
     * Returns the security.yml roles hierarchy.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRolesForChoiceList(): array
    {
        $roles = array_combine(array_keys($this->getRoles()), array_keys($this->getRoles()));

        return array_merge([
            'ROLE_ADMIN'       => null,
            'ROLE_DPO'         => null,
            'ROLE_VIEWER'      => null,
            'ROLE_CONTROLLER'  => null,
            'ROLE_SUPER_ADMIN' => null,
        ], $roles);
    }
}
