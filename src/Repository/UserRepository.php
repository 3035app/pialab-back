<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends EntityRepository
{
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->where('u.username = :usernameOrEmail')
            ->orWhere('u.email = :usernameOrEmail')
        ;

        $qb->setParameter('usernameOrEmail', $usernameOrEmail);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
