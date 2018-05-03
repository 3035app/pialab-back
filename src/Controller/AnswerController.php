<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Answer;

class AnswerController extends PiaSubController
{
    protected static $DATA_KEY = 'answer';

    /**
     * @FOSRest\Get("/pias/{piaId}/answers")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @FOSRest\Get("/pias/{piaId}/answers/{id}")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Post("/pias/{piaId}/answers")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @FOSRest\Put("/pias/{piaId}/answers/{id}")
     * @FOSRest\Patch("/pias/{piaId}/answers/{id}")
     * @FOSRest\Post("/pias/{piaId}/answers/{id}")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Delete("pias/{piaId}/answers/{id}")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Answer::class;
    }
}
