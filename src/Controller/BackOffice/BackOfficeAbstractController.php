<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class BackOfficeAbstractController extends Controller
{
    protected function buildPager(
        Request $request,
        string $entityClass,
        ?int $defaultLimit = 20,
        ?string $pageParameter = 'page',
        ?string $limitParameter = 'limit'
    ): PagerfantaInterface {
        $queryBuilder = $this->getDoctrine()->getRepository($entityClass)->createQueryBuilder('e');

        $queryBuilder
            ->orderBy('e.id', 'DESC');

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $page = $request->get($pageParameter, 1);
        $limit = $request->get($limitParameter, $defaultLimit);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $pagerfanta;
    }

    public function getQueryRedirectUrl(Request $request): ?string
    {
        return $request->query->get('redirect', null) ?? $request->request->get('redirect', null);
    }
}
