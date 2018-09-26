<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use PiaApi\Services\ProcessingService;
use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\Folder;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\Exception\ApiException;
use JMS\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ProcessingController extends RestController
{
    /**
     * @var ProcessingService
     */
    protected $processingService;

    /**
     * @var jsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        ProcessingService $processingService,
        JsonToEntityTransformer $jsonToEntityTransformer,
        SerializerInterface $serializer
    ) {
        parent::__construct($propertyAccessor);
        $this->processingService = $processingService;
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
        $this->serializer = $serializer;
    }

    protected function getEntityClass()
    {
        return Processing::class;
    }

    /**
     * Lists all Processings reachable by the user.
     *
     * @Swg\Tag(name="Processing")
     *
     * @FOSRest\Get("/processings")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Processings",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Processing::class, groups={"Default"}))
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
            ->getPaginatedProcessingsByStructure($structure);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one Processing by its ID.
     *
     * @Swg\Tag(name="Processing")
     *
     * @FOSRest\Get("/processings/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Processing"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Processing",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Processing::class, groups={"Default"})
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
     * Creates a Processing.
     *
     * @Swg\Tag(name="Processing")
     *
     * @FOSRest\Post("/processings")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="Processing",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         required={"name", "author", "controllers"},
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="author", type="string"),
     *         @Swg\Property(property="status", type="number"),
     *         @Swg\Property(property="description", type="string"),
     *         @Swg\Property(property="life_cycle", type="string"),
     *         @Swg\Property(property="storage", type="string"),
     *         @Swg\Property(property="standards", type="string"),
     *         @Swg\Property(property="processors", type="string"),
     *         @Swg\Property(property="controllers", type="string"),
     *         @Swg\Property(property="non_eu_transfer", type="string"),
     *         @Swg\Property(property="processing_data_types", type="array", @Swg\Items(
     *              ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"})
     *         )),
     *     ),
     *     description="The Processing content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Processing",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Processing::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_PROCESSING')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $entity = $this->serializer->deserialize($request->getContent(), $this->getEntityClass(), 'json');
        $folder = $this->getResource($entity->getFolder()->getId(), Folder::class);

        $processing = $this->processingService->createProcessing(
            $request->get('name'),
            $folder,
            $request->get('author'),
            $request->get('controllers')
        );

        $this->persist($processing);

        return $this->view($processing, Response::HTTP_OK);
    }

    /**
     * Updates a processing.
     *
     * @Swg\Tag(name="Processing")
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
     *     description="The ID of the Processing"
     * )
     * @Swg\Parameter(
     *     name="full Processing",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="author", type="string"),
     *         @Swg\Property(property="status", type="number"),
     *         @Swg\Property(property="description", type="string"),
     *         @Swg\Property(property="life_cycle", type="string"),
     *         @Swg\Property(property="storage", type="string"),
     *         @Swg\Property(property="standards", type="string"),
     *         @Swg\Property(property="processors", type="string"),
     *         @Swg\Property(property="controllers", type="string"),
     *         @Swg\Property(property="non_eu_transfer", type="string"),
     *         @Swg\Property(property="processing_data_types", type="array", @Swg\Items(
     *              ref=@Nelmio\Model(type=ProcessingDataType::class, groups={"Default"})
     *         )),
     *     ),
     *     description="The Processing content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Processing",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Processing::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/processings/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_PROCESSING')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $processing = $this->getResource($id);
        $this->canAccessResourceOr403($processing);

        $updatableAttributes = [
            'folder'            => Folder::class,
            'name'              => RequestDataHandler::TYPE_STRING,
            'author'            => RequestDataHandler::TYPE_STRING,
            'description'       => RequestDataHandler::TYPE_STRING,
            'processors'        => RequestDataHandler::TYPE_STRING,
            'controllers'       => RequestDataHandler::TYPE_STRING,
            'non_eu_transfer'   => RequestDataHandler::TYPE_STRING,
            'life_cycle'        => RequestDataHandler::TYPE_STRING,
            'storage'           => RequestDataHandler::TYPE_STRING,
            'standards'         => RequestDataHandler::TYPE_STRING,
            'status'            => RequestDataHandler::TYPE_INT,
        ];

        $this->mergeFromRequest($processing, $updatableAttributes, $request);

        $this->update($processing);

        return $this->view($processing, Response::HTTP_OK);
    }

    /**
     * Deletes a processing.
     *
     * @Swg\Tag(name="Processing")
     *
     * @FOSRest\Delete("/processings/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Processing"
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
        $processing = $this->getResource($id);
        $this->canAccessResourceOr403($processing);

        if (count($processing->getPias()) > 0) {
            throw new ApiException(Response::HTTP_CONFLICT, 'Processing must not contain Pias before being deleted', 701);
        }

        $this->remove($processing);

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * Exports a PROCESSING.
     *
     * @Swg\Tag(name="Processing")
     *
     * @FOSRest\Get("/processings/{id}/export", requirements={"id"="\d+"})
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
     *     description="The ID of the Processing"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns a PROCESSING",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Processing::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EXPORT_PIA')")
     *
     * @return array
     */
    public function exportAction(Request $request, $id)
    {
        $processing = $this->getResource($id);
        $this->canAccessResourceOr403($processing);

        $serializedPia = $this->jsonToEntityTransformer->entityToJson($processing);

        return new Response($serializedPia, Response::HTTP_OK);
    }
}
