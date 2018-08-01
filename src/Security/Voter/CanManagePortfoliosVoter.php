<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use PiaApi\Security\Role\RoleHierarchy;

class CanManagePortfoliosVoter extends Voter
{
    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    private const CAN_MANAGE_PORTFOLIOS = 'CAN_MANAGE_PORTFOLIOS';
    private const CAN_MANAGE_ONLY_OWNED_PORTFOLIOS = 'CAN_MANAGE_ONLY_OWNED_PORTFOLIOS';

    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    protected function supports($attribute, $subject)
    {
        return $attribute === self::CAN_MANAGE_ONLY_OWNED_PORTFOLIOS;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //if the user CAN_MANAGE_PORTFOLIOS he is not granted for CAN_MANAGE_ONLY_OWNED_PORTFOLIOS
        return !$this->roleHierarchy->isGranted($token->getUser(), self::CAN_MANAGE_PORTFOLIOS);
    }
}
