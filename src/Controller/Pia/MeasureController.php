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
use PiaApi\Entity\Pia\Measure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;

class MeasureController extends PiaSubController
{
    /**
     * Lists all Answers for a specific Treatment.
     *
     * @Swg\Tag(name="Measure")
     *
     * @FOSRest\Get("/pias/{piaId}/measures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Answers for given Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Measure::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_MEASURE')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * Shows one Measure by its ID and specific Treatment.
     *
     * @Swg\Tag(name="Measure")
     *
     * @FOSRest\Get("/pias/{piaId}/measures/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Measure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Measure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_MEASURE')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * Creates a Measure for a specific Treatment.
     *
     * @Swg\Tag(name="Measure")
     *
     * @FOSRest\Post("/pias/{piaId}/measures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Measure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Measure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_MEASURE')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * Updates a Measure for a specific Treatment.
     *
     * @Swg\Tag(name="Measure")
     *
     * @FOSRest\Put("/pias/{piaId}/measures/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Measure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Measure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_MEASURE')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * Deletes a Measure for a specific Treatment.
     *
     * @Swg\Tag(name="Measure")
     *
     * @FOSRest\Delete("pias/{piaId}/measures/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_MEASURE')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Measure::class;
    }
}
