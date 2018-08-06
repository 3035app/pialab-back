<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Repository;

use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ProcessingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Processing::class);
    }

    public function getPaginatedProcessingsByStructure(
        ?Structure $structure,
        ?int $defaultLimit = 20,
        ?int $page = 1
    ): array {
        $queryBuilder = $this->createQueryBuilder('pp');

        $queryBuilder
            ->innerJoin('pp.folder', 'f')
            ->where('f.structure IN (:structure)')
            ->orderBy('pp.createdAt')
            ->setParameter('structure', $structure)
        ;

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta->getCurrentPageResults()->getArrayCopy();
    }
}
