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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\ProcessingAttachment;

class ProcessingAttachmentController extends RestController
{
    public function __construct(PropertyAccessorInterface $propertyAccessor) {
        parent::__construct($propertyAccessor);
    }

    protected function getEntityClass()
    {
        return ProcessingAttachment::class;
    }

    /**
     * Lists all Attachments for a specific Processing.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/processings/{processingId}/attachments")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="processingId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the PROCESSING"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Attachments of given Processing",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Attachment::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING')")
     */
    public function listAction(Request $request, $processingId)
    {
        $this->canAccessRouteOr403();

        $criteria = $this->extractCriteria($request, ['processing' => $processingId]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one Attachment by its ID and specific Processing.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Get("/processings/{processingId}/attachments/{id}")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="processingId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Processing"
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
     *         ref=@Nelmio\Model(type=ProcessingAttachment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING')")
     */
    public function showAction(Request $request, $processingId, $id)
    {
        $this->canAccessRouteOr403();

        $entity = $this->getRepository()->find($id);
        if ($entity === null) {
            return $this->view($entity, Response::HTTP_NOT_FOUND);
        }

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * Creates an Attachment for a specific Processing.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Post("/processings/{processingId}/attachments")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="processingId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Processing"
     * )
     * @Swg\Parameter(
     *     name="Attachment",
     *     in="body",
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
     *         ref=@Nelmio\Model(type=ProcessingAttachment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_PROCESSING')")
     */
    public function createAction(Request $request, $processingId)
    {
        $this->canAccessRouteOr403();

        $entity = $this->newFromRequest($request, null, $processingId);
        $this->persist($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * Deletes an Attachment for a specific Processing.
     *
     * @Swg\Tag(name="Attachment")
     *
     * @FOSRest\Delete("processings/{processingId}/attachments/{id}")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="processingId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the Processing"
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
     * @Security("is_granted('CAN_EDIT_PROCESSING')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $processingId, $id)
    {
        $this->canAccessRouteOr403();

        $entity = $this->getRepository()->find($id);
        $this->remove($entity);

        return $this->view([], Response::HTTP_OK);
    }
}
