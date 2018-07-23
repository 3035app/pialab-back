<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Entity\Pia\Evaluation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;

class EvaluationController extends PiaSubController
{
    /**
     * Lists all Answers for a specific Treatment.
     *
     * @Swg\Tag(name="Evaluation")
     *
     * @FOSRest\Get("/pias/{piaId}/evaluations")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Answers of given Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Evaluation::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_EVALUATION')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * Shows one Evaluation by its ID and specific Treatment.
     *
     * @Swg\Tag(name="Evaluation")
     *
     * @FOSRest\Get("/pias/{piaId}/evaluations/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Evaluation",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Evaluation::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_EVALUATION')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * Creates an Evaluation for a specific Treatment.
     *
     * @Swg\Tag(name="Evaluation")
     *
     * @FOSRest\Post("/pias/{piaId}/evaluations")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Evaluation",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Evaluation::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_EVALUATION')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * Updates an Evaluation for a specific Treatment.
     *
     * @Swg\Tag(name="Evaluation")
     *
     * @FOSRest\Put("/pias/{piaId}/evaluations/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Evaluation",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Evaluation::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_EVALUATION')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * Deletes an Evaluation for a specific Treatment.
     *
     * @Swg\Tag(name="Evaluation")
     *
     * @FOSRest\Delete("pias/{piaId}/evaluations/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
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
