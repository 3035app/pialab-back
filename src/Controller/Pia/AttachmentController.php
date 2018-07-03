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
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/pias/{piaId}/attachments")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Attachments for a specific Treatment",
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
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/pias/{piaId}/attachments/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Attachment by its id and for a specific Treatment",
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
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Post("/pias/{piaId}/attachments")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates an Attachment for a specific Treatment",
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
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Put("/pias/{piaId}/attachments/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update an Attachment for a specific Treatment",
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
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Delete("pias/{piaId}/attachments/{id}")

     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete an Attachment for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Attachment::class, groups={"Default"})
     *     )
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
