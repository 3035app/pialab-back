<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use PiaApi\Entity\Pia\Structure;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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

    /**
     * @param Structure $structure
     * @param int       $defaultLimit
     *
     * @return PagerfantaInterface
     */
    public function getPaginatedUsersByStructure(
            Structure $structure,
            ?int $defaultLimit = 20
        ): PagerfantaInterface {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder
                ->orderBy('e.id', 'DESC')
                ->where('e.structure = :structure')
                ->setParameter('structure', $structure);

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);

        return $pagerfanta;
    }
}
