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
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\StructureType;
use PiaApi\Services\StructureService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class StructureController extends RestController
{
    /**
     * @var StructureService
     */
    private $structureService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        StructureService $structureService
    ) {
        parent::__construct($propertyAccessor);
        $this->structureService = $structureService;
    }

    /**
     * Lists all Structures.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures")
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
     *     description="Returns all Structures",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Structure::class, groups={"Default"}))
     *     )
     * )
     * @Security("is_granted('CAN_SHOW_STRUCTURE')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $criteria = array_merge($this->extractCriteria($request), ['limit' => 20, 'page' => 1]);

        /* @var Pagerfanta $structuresPager */
        $structuresPager;
        $portfolios = null;

        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES')) {
            $portfolios = $this->getUser()->getPortfolios();
        }

        if ($portfolios && count($portfolios) > 0) {
            $structuresPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedStructuresForPortfolios($portfolios, $criteria['limit'], $criteria['page']);
        } else {
            $structuresPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedStructures($criteria['limit'], $criteria['page']);
        }

        return $this->view($structuresPager->getCurrentPageResults()->getArrayCopy(), Response::HTTP_OK);
    }

    /**
     * Shows one Structure by its ID.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Structure"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_STRUCTURE')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->getRepository()->find($id);

        if ($entity === null) {
            return $this->view($entity, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * Creates a Structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Post("/structures")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="Structure",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         required={"name"},
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="structureType", type="number"),
     *         @Swg\Property(property="portfolio", type="number")
     *     ),
     *     description="The Structure content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_STRUCTURE')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $structureType = $this->getRepository(StructureType::class)->find($request->get('structureType', -1));
        $portfolio = $this->getRepository(Portfolio::class)->find($request->get('portfolio', -1));

        $structure = $this->structureService->createStructure(
            $request->get('name'),
            $structureType,
            $portfolio
        );

        $this->persist($structure);

        return $this->view($structure, Response::HTTP_OK);
    }

    /**
     * Updates a structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Put("/structures/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Structure"
     * )
     * @Swg\Parameter(
     *     name="Structure",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="type", type="object", ref=@Nelmio\Model(type=StructureType::class, groups={"Default"})),
     *         @Swg\Property(property="portfolio", type="object", ref=@Nelmio\Model(type=Portfolio::class, groups={"Default"})),
     *         @Swg\Property(property="address", type="string"),
     *         @Swg\Property(property="phone", type="string"),
     *         @Swg\Property(property="siren", type="string"),
     *         @Swg\Property(property="siret", type="string"),
     *         @Swg\Property(property="vat_number", type="string"),
     *         @Swg\Property(property="activity_code", type="string"),
     *         @Swg\Property(property="executive", type="string"),
     *         @Swg\Property(property="backup", type="string"),
     *         @Swg\Property(property="dpo", type="string"),
     *         @Swg\Property(property="legal_form", type="string"),
     *         @Swg\Property(property="registration_date", type="string")
     *     ),
     *     description="The Structure content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_STRUCTURE')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $structure = $this->getResource($id);
        $this->canAccessResourceOr403($structure);

        $updatableAttributes = [
            'name'              => RequestDataHandler::TYPE_STRING,
            'type'              => StructureType::class,
            'portfolio'         => Portfolio::class,
            'address'           => RequestDataHandler::TYPE_STRING,
            'phone'             => RequestDataHandler::TYPE_STRING,
            'siren'             => RequestDataHandler::TYPE_STRING,
            'siret'             => RequestDataHandler::TYPE_STRING,
            'vat_number'        => RequestDataHandler::TYPE_STRING,
            'activity_code'     => RequestDataHandler::TYPE_STRING,
            'executive'         => RequestDataHandler::TYPE_STRING,
            'backup'            => RequestDataHandler::TYPE_STRING,
            'dpo'               => RequestDataHandler::TYPE_STRING,
            'legal_form'        => RequestDataHandler::TYPE_STRING,
            'registration_date' => \DateTime::class,
        ];

        $this->mergeFromRequest($structure, $updatableAttributes, $request);

        $this->update($structure);

        return $this->view($structure, Response::HTTP_OK);
    }

    /**
     * Deletes a structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Delete("/structures/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Structure"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_STRUCTURE')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $structure = $this->getResource($id);
        $this->canAccessResourceOr403($structure);

        $this->remove($structure);

        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Structure::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES') && count($this->getUser()->getPortfolios()) > 0 && in_array($resource, $this->getUser()->getPortfolioStructures())) {
            throw new AccessDeniedHttpException('Resource not found');
        }
    }
}
