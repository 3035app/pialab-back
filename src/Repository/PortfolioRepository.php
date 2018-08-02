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
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Entity\Oauth\User;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PortfolioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portfolio::class);
    }

    /**
     * @param User $user
     *
     * @return PagerfantaInterface
     */
    public function getPaginatedByUser(User $user): PagerfantaInterface
    {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder
                ->orderBy('e.id', 'DESC')
                ->leftJoin('e.users', 'u')
                ->where('u.id = :user_id')
                ->setParameter('user_id', $user->getId());

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        return $pagerfanta;
    }
}
