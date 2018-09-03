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
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PiaApi\Entity\Pia\Attachment;

class AttachmentController extends PiaSubController
{
    /**
     * Lists all Attachments for a specific Treatment.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/pias/{piaId}/attachments")
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
     *     description="Returns all Attachments of given Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Attachment::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PIA')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * Shows one Attachment by its ID and specific Treatment.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/pias/{piaId}/attachments/{id}")
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
     *     description="The ID of the Attachment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Attachment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Attachment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PIA')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * Creates an Attachment for a specific Treatment.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Post("/pias/{piaId}/attachments")
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
     *     name="Attachment",
     *     in="body",
     *     type="json",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         required={"file", "mime_type", "name"},
     *         @Swg\Property(property="file", type="string"),
     *         @Swg\Property(property="mime_type", type="string"),
     *         @Swg\Property(property="name", type="string")
     *     ),
     *     description="The Attachment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Attachment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Attachment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_PIA')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * Updates an Attachment for a specific Treatment.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Put("/pias/{piaId}/attachments/{id}")
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
     *     description="The ID of the Attachment"
     * )
     * @Swg\Parameter(
     *     name="Attachment",
     *     in="body",
     *     type="json",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="file", type="string"),
     *         @Swg\Property(property="mime_type", type="string"),
     *         @Swg\Property(property="name", type="string")
     *     ),
     *     description="The Attachment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Return the updated Attachment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Attachment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_PIA')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * Deletes an Attachment for a specific Treatment.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Delete("pias/{piaId}/attachments/{id}")
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
     *     description="The ID of the Attachment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_EDIT_PIA')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Attachment::class;
    }
}
