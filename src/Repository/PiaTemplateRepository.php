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
use PiaApi\Entity\Pia\Structure;

class PiaTemplateRepository extends EntityRepository
{
    public function findAvailablePiaTemplatesForStructure(?Structure $structure): array
    {
        $qb = $this->createQueryBuilder('pt');

        $qb
            ->leftJoin('pt.structures', 'structures')
            ->leftJoin('pt.structureTypes', 'structureTypes')
            ->set('pt.enabled', true);

        if ($structure !== null) {
            $qb
                ->where($qb->expr()->in('structures', ':structure'))
                ->orWhere($qb->expr()->in('structureTypes', ':structureType'));

            $qb->setParameters([
                'structure'     => $structure,
                'structureType' => $structure->getType(),
            ]);
        }

        return $qb->getQuery()->getResult();
    }
}
