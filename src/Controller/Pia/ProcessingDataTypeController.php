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
use PiaApi\Services\ProcessingDataTypeService;
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\Entity\Pia\Processing;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ProcessingDataTypeController extends RestController
{
    /**
     * @var ProcessingDataTypeService
     */
    private $processingDataTypeService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        ProcessingDataTypeService $processingDataTypeService
    ) {
        parent::__construct($propertyAccessor);
        $this->processingDataTypeService = $processingDataTypeService;
    }

    protected function getEntityClass()
    {
        return ProcessingDataType::class;
    }

    /**
     * Lists all ProcessingDataTypes reachable by the user.
     *
     * @Swg\Tag(name="ProcessingDataType")
     *
     * @FOSRest\Get("/processing-data-types")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all ProcessingDataTypes",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $structure = $this->getUser()->getStructure();

        $collection = $this->getRepository()
            ->getPaginatedProcessingDataTypesByStructure($structure);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one ProcessingDataType by its ID.
     *
     * @Swg\Tag(name="ProcessingDataType")
     *
     * @FOSRest\Get("/processing-data-types/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one ProcessingDataType",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"})
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
     * Creates a ProcessingDataType.
     *
     * @Swg\Tag(name="ProcessingDataType")
     *
     * @FOSRest\Post("/processing-data-types")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created ProcessingDataType",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"})
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
        $reference = $request->get('reference', null);
        $retention = $request->get('retention_period', null);
        $sensitive = $request->get('sensitive', null);

        $processingDataType = $this->processingDataTypeService->createProcessingDataType(
            $processing,
            $reference
        );

        $processingDataType->setRetentionPeriod($retention);
        $processingDataType->setSensitive($sensitive);

        $this->persist($processingDataType);

        return $this->view($processingDataType, Response::HTTP_OK);
    }

    /**
     * Updates a ProcessingDataType.
     *
     * @Swg\Tag(name="ProcessingDataType")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated ProcessingDataType",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/processing-data-types/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_PROCESSING')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $processingDataType = $this->getResource($id);
        $this->canAccessResourceOr403($processingDataType);

        $updatableAttributes = [
            'reference'         => RequestDataHandler::TYPE_STRING,
            'data'              => RequestDataHandler::TYPE_ARRAY,
            'retention_period'  => RequestDataHandler::TYPE_STRING,
            'sensitive'         => RequestDataHandler::TYPE_BOOL,
        ];

        $this->mergeFromRequest($processingDataType, $updatableAttributes, $request);

        $this->update($processingDataType);

        return $this->view($processingDataType, Response::HTTP_OK);
    }

    /**
     * Deletes a ProcessingDataType.
     *
     * @Swg\Tag(name="ProcessingDataType")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @FOSRest\Delete("/processing-data-types/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_DELETE_PROCESSING')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $processingDataType = $this->getResource($id);
        $this->canAccessResourceOr403($processingDataType);

        $this->remove($processingDataType);

        return $this->view([], Response::HTTP_OK);
    }
}
