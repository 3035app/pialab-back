<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Oauth\User;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

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
     * @param int|null  $defaultLimit
     * @param int|null  $page
     *
     * @return PagerfantaInterface
     */
    public function getPaginatedUsersByStructure(
        Structure $structure,
        ?int $defaultLimit = 20,
        ?int $page = 0
    ): PagerfantaInterface {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder
                ->orderBy('e.id', 'DESC')
                ->where('e.structure = :structure')
                ->setParameter('structure', $structure);

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * @param int|null $defaultLimit
     * @param int|null $page
     *
     * @return PagerfantaInterface
     */
    public function getPaginatedUsers(?int $defaultLimit = 20, ?int $page = 0): PagerfantaInterface
    {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder
            ->orderBy('e.id', 'DESC');

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }
}
