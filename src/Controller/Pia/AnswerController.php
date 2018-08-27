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
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Entity\Pia\Answer;

class AnswerController extends PiaSubController
{
    /**
     * Lists all Answers for a specific Treatment.
     *
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Get("/pias/{piaId}/answers")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Answers of given Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Answer::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_ANSWER')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * Shows a specific Answer by its ID and specific Treatment.
     *
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Get("/pias/{piaId}/answers/{id}")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="piaId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the PIA"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Answer"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Answer",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_ANSWER')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * Creates an Answer for a specific Treatment.
     *
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Post("/pias/{piaId}/answers")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="piaId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the PIA"
     * )
     * @Swg\Parameter(
     *     name="Answer",
     *     in="body",
     *     type="json",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @SWG\Property(property="data", type="object", @SWG\Property(property="text", type="string")),
     *         @SWG\Property(property="reference_to", type="string")
     *     ),
     *     description="The Answer text content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Answer",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_ANSWER')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * Updates an Answer for a specific Treatment.
     *
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Put("/pias/{piaId}/answers/{id}")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="piaId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the PIA"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Answer"
     * )
     * @Swg\Parameter(
     *     name="Answer",
     *     in="body",
     *     type="json",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @SWG\Property(property="data", type="object", @SWG\Property(property="text", type="string")),
     *         @SWG\Property(property="reference_to", type="string")
     *     ),
     *     description="The Answer text content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Answer",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_ANSWER')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * Delete an Answer for a specific Treatment.
     *
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Delete("pias/{piaId}/answers/{id}")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="piaId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the PIA"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Answer"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_ANSWER')")
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
