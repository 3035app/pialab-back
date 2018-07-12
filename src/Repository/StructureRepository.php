<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Repository;

use PiaApi\Entity\Pia\Structure;
use Doctrine\Common\Persistence\ManagerRegistry;
use PiaApi\Entity\Pia\Portfolio;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class StructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    /**
     * Fetch a structure from name or Id.
     *
     * @param string|int $nameOrId
     *
     * @return Structure|null
     */
    public function findOneByNameOrId($nameOrId): ?Structure
    {
        $qb = $this->createQueryBuilder('s');

        $field = is_numeric($nameOrId) ? 'id' : 'name';

        $qb
            ->where('s.' . $field . ' = :nameOrId')
        ;

        $qb->setParameter('nameOrId', $nameOrId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Portfolio $portfolio
     * @param int       $defaultLimit
     *
     * @return PagerfantaInterface
     */
    public function getPaginatedStructuresByPortfolio(
        Portfolio $portfolio,
        ?int $defaultLimit = 20
    ): PagerfantaInterface {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder
            ->orderBy('e.id', 'DESC')
            ->where('e.portfolio = :portfolio')
            ->setParameter('portfolio', $portfolio);

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);

        return $pagerfanta;
    }
}
