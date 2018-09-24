<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Services\ProcessingCommentService;
use PiaApi\Entity\Pia\ProcessingComment;
use PiaApi\Entity\Pia\Processing;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ProcessingCommentController extends RestController
{
    public function __construct(PropertyAccessorInterface $propertyAccessor) {
        parent::__construct($propertyAccessor);
    }

    protected function getEntityClass()
    {
        return ProcessingComment::class;
    }

    /**
     * Lists all ProcessingComments for a specific Processing.
     *
     * @Swg\Tag(name="ProcessingComment")
     *
     * @FOSRest\Get("/processings/{processingId}/comments")
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
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Comments of given Processing",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=ProcessingComment::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function listAction(Request $request, $processingId)
    {
        $this->canAccessRouteOr403();

        $criteria = $this->extractCriteria($request, ['processing' => $processingId]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one ProcessingComment by its ID.
     *
     * @Swg\Tag(name="ProcessingComment")
     *
     * @FOSRest\Get("/processing-comments/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the ProcessingComment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one ProcessingComment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingComment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        return $this->showEntity($id);
    }

    /**
     * Creates a ProcessingComment.
     *
     * @Swg\Tag(name="ProcessingComment")
     *
     * @FOSRest\Post("/processing-comments")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="ProcessingComment",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         required={"reference"},
     *         @Swg\Property(property="reference", type="string"),
     *         @Swg\Property(property="data", type="string"),
     *         @Swg\Property(property="retention_period", type="string"),
     *         @Swg\Property(property="sensitive", type="boolean")
     *     ),
     *     description="The ProcessingComment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created ProcessingComment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingComment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_PROCESSING')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $processing = $this->getResource($request->get('processing_id', -1), Processing::class);
        $content = $request->get('content', null);
        $field = $request->get('field', null);

        $processingComment = new ProcessingComment($processing, $content, $field);

        $this->persist($processingComment);

        return $this->view($processingComment, Response::HTTP_OK);
    }

    /**
     * Updates a ProcessingComment.
     *
     * @Swg\Tag(name="ProcessingComment")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the ProcessingComment"
     * )
     * @Swg\Parameter(
     *     name="ProcessingComment",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="content", type="string"),
     *         @Swg\Property(property="field", type="string"),
     *     ),
     *     description="The ProcessingComment content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated ProcessingComment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingComment::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/processing-comments/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_PROCESSING')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $processingComment = $this->getResource($id);
        $this->canAccessResourceOr403($processingComment);

        $updatableAttributes = [
            'reference'         => RequestDataHandler::TYPE_STRING,
            'data'              => RequestDataHandler::TYPE_STRING,
            'retention_period'  => RequestDataHandler::TYPE_STRING,
            'sensitive'         => RequestDataHandler::TYPE_BOOL,
        ];

        $this->mergeFromRequest($processingComment, $updatableAttributes, $request);

        $this->update($processingComment);

        return $this->view($processingComment, Response::HTTP_OK);
    }

    /**
     * Deletes a ProcessingComment.
     *
     * @Swg\Tag(name="ProcessingComment")
     *
     * @FOSRest\Delete("/processing-comments/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the ProcessingComment"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_PROCESSING')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $processingComment = $this->getResource($id);
        $this->canAccessResourceOr403($processingComment);

        $this->remove($processingComment);

        return $this->view([], Response::HTTP_OK);
    }
}
