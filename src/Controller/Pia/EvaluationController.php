<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Evaluation;

class EvaluationController extends PiaSubController
{
    /**
     * @FOSRest\Get("/pias/{piaId}/evaluations")
     * @Security("is_granted('CAN_SHOW_EVALUATION')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @FOSRest\Get("/pias/{piaId}/evaluations/{id}")
     * @Security("is_granted('CAN_SHOW_EVALUATION')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Post("/pias/{piaId}/evaluations")
     * @Security("is_granted('CAN_CREATE_EVALUATION')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @FOSRest\Put("/pias/{piaId}/evaluations/{id}")
     * @FOSRest\Patch("/pias/{piaId}/evaluations/{id}")
     * @FOSRest\Post("/pias/{piaId}/evaluations/{id}")
     * @Security("is_granted('CAN_EDIT_EVALUATION')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Delete("pias/{piaId}/evaluations/{id}")
     * @Security("is_granted('CAN_DELETE_EVALUATION')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Evaluation::class;
    }
}
