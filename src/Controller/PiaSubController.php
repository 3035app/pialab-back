<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use PiaApi\Entity\Pia\Pia;
use PiaApi\Entity\Pia\Answer;
use PiaApi\Entity\Pia\Attachment;
use PiaApi\Entity\Pia\Comment;
use PiaApi\Entity\Pia\Evaluation;
use PiaApi\Entity\Pia\Measure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class PiaSubController extends RestController
{
    protected $entityClasses = [
        Answer::class,
        Attachment::class,
        Comment::class,
        Evaluation::class,
        Measure::class,
    ];

    public function listAction(Request $request, $piaId)
    {
        $this->canAccessRouteOr304();

        $criteria = $this->extractCriteria($request, ['pia' => $piaId]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    public function showAction(Request $request, $piaId, $id)
    {
        $this->canAccessRouteOr304();

        $entity = $this->getRepository()->find($id);
        if ($entity === null) {
            return $this->view($entity, Response::HTTP_NOT_FOUND);
        }

        return $this->view($entity, Response::HTTP_OK);
    }

    public function createAction(Request $request, $piaId)
    {
        $this->canAccessRouteOr304();

        $entity = $this->newFromRequest($request, $piaId);
        $this->persist($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function updateAction(Request $request, $piaId, $id)
    {
        $this->canAccessRouteOr304();

        $entity = $this->newFromRequest($request, $piaId);
        $this->update($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function deleteAction(Request $request, $piaId, $id)
    {
        $this->canAccessRouteOr304();

        $entity = $this->getRepository()->find($id);
        $this->remove($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function canAccessResourceOr304($resource): void
    {
        if (!in_array(get_class($resource), $this->entityClasses)) {
            return;
        }

        if ($resource->getPia()->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
