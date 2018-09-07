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
use PiaApi\Entity\Pia\Structure;

class ProcessingTemplateRepository extends EntityRepository
{
    public function findAvailableProcessingTemplatesForStructure(?Structure $structure): array
    {
        $qb = $this->createQueryBuilder('pt');

        $qb
            ->leftJoin('pt.structures', 'structures')
            ->leftJoin('pt.structureTypes', 'structureTypes');

        if ($structure !== null) {
            $or = $qb
                ->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('pt.enabled', ':enabled'),
                        $qb->expr()->in('structures', ':structure')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('pt.enabled', ':enabled'),
                        $qb->expr()->in('structureTypes', ':structureType')
                    )
                )
            ;

            $qb->where($or);

            $qb->setParameter('structure', $structure);
            $qb->setParameter('structureType', $structure->getType());
            $qb->setParameter('enabled', true);
        } else {
            $or = $qb
                ->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('pt.enabled', ':enabled'),
                        $qb->expr()->isNull('structures')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('pt.enabled', ':enabled'),
                        $qb->expr()->isNull('structureType')
                    )
                )
            ;

            $qb->where($or);

            $qb->setParameter('enabled', true);
        }

        return $qb->getQuery()->getResult();
    }
}
