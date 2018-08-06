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
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\Entity\Pia\Structure;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ProcessingDataTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessingDataType::class);
    }

    public function getPaginatedProcessingDataTypesByStructure(
        ?Structure $structure,
        ?int $defaultLimit = 20,
        ?int $page = 1
    ): array {
        $queryBuilder = $this->createQueryBuilder('pdt');

        $queryBuilder
            ->innerJoin('pdt.processing', 'p')
            ->innerJoin('p.folder', 'f')
            ->where('f.structure IN (:structure)')
            ->setParameter('structure', $structure)
        ;

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($defaultLimit);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta->getCurrentPageResults()->getArrayCopy();
    }

}
