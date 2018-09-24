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
use PiaApi\Entity\Pia\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends PiaSubController
{
    /**
     * Lists all Comments for a specific Treatment.
     *
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Get("/pias/{piaId}/comments")
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
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Comments of given Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Comment::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * Shows one Comment by its ID and specific Treatment.
     *
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Get("/pias/{piaId}/comments/{id}")
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
     *     description="The ID of the Comment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Comment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * Creates a Comment for a specific Treatment.
     *
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Post("/pias/{piaId}/comments")
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
     *     name="Comment",
     *     in="body",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         required={"description", "reference_to"},
     *         @Swg\Property(property="description", type="string"),
     *         @Swg\Property(property="for_measure", type="boolean"),
     *         @Swg\Property(property="reference_to", type="string")
     *     ),
     *     description="The Comment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Comment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_COMMENT')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * Updates a Comment for a specific Treatment.
     *
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Put("/pias/{piaId}/comments/{id}")
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
     *     description="The ID of the Comment"
     * )
     * @Swg\Parameter(
     *     name="Comment",
     *     in="body",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="description", type="string"),
     *         @Swg\Property(property="for_measure", type="boolean"),
     *         @Swg\Property(property="reference_to", type="string")
     *     ),
     *     description="The Comment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Comment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_COMMENT')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * Deletes a Comment for a specific Treatment.
     *
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Delete("pias/{piaId}/comments/{id}")
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
     *     description="The ID of the Comment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_COMMENT')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Comment::class;
    }
}
