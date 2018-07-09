<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use PiaApi\Entity\Pia\Pia;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Entity\Pia\PiaTemplate;
use PiaApi\Entity\Pia\Folder;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;

class PiaController extends RestController
{
    /**
     * @var jsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        JsonToEntityTransformer $jsonToEntityTransformer
    ) {
        parent::__construct($propertyAccessor);
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
    }

    /**
     * @Swg\Tag(name="Treatment")
     *
     * @FOSRest\Get("/pias")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all PIAs",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Pia::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PIA')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $structure = $this->getUser()->getStructure();

        $criteria = array_merge($this->extractCriteria($request), ['structure' => $structure]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Treatment")
     *
     * @FOSRest\Get("/pias/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one PIA by its id",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Pia::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PIA')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        $pia = $this->getRepository()->find($id);
        if ($pia === null) {
            return $this->view($pia, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Treatment")
     *
     * @FOSRest\Post("/pias")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates a treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Pia::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_PIA')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $pia = $this->newFromRequest($request);

        if ($request->get('folder') !== null) {
            $folderId = $request->get('folder')['id'];
            $folder = $this->getResource($folderId, Folder::class);
        } else {
            $folder = $this->getUser()->getStructure() ? $this->getUser()->getStructure()->getRootFolder() : null;
        }
        $pia->setFolder($folder);
        $pia->setStructure($this->getUser()->getStructure());
        $this->persist($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias/new-from-template/{id}")
     * @Security("is_granted('CAN_CREATE_PIA')")
     *
     * @return array
     */
    public function createFromTemplateAction(Request $request, $id)
    {
        /** @var PiaTemplate $piaTemplate */
        $piaTemplate = $this->getDoctrine()->getRepository(PiaTemplate::class)->find($id);
        if ($piaTemplate === null) {
            return $this->view($piaTemplate, Response::HTTP_NOT_FOUND);
        }

        $pia = $this->jsonToEntityTransformer->transform($piaTemplate->getData());
        if (($folderId = $request->get('folder')) !== null) {
            $folder = $this->getResource($request->get('folder')['id'], Folder::class);
        } else {
            $folder = $this->getUser()->getStructure() ? $this->getUser()->getStructure()->getRootFolder() : null;
        }
        $pia->setFolder($folder);
        $pia->setName($request->get('name', $pia->getName()));
        $pia->setAuthorName($request->get('author_name', $pia->getAuthorName()));
        $pia->setEvaluatorName($request->get('evaluator_name', $pia->getEvaluatorName()));
        $pia->setValidatorName($request->get('validator_name', $pia->getValidatorName()));
        $pia->setStructure($this->getUser()->getStructure());
        $this->persist($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Treatment")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update a treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Pia::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/pias/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_PIA')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $pia = $this->getResource($id);
        $this->canAccessResourceOr403($pia);

        $updatableAttributes = [
            'name'                              => 'string',
            'author_name'                       => 'string',
            'evaluator_name'                    => 'string',
            'validator_name'                    => 'string',
            'folder'                            => Folder::class,
            'dpo_status'                        => 'int',
            'concerned_people_status'           => 'int',
            'status'                            => 'int',
            'dpo_opinion'	                      => 'string',
            'concerned_people_opinion'	         => 'string',
            'concerned_people_searched_opinion' => 'boolean',
            'concerned_people_searched_content' => 'string',
            'rejection_reason'	                 => 'string',
            'applied_adjustments'	              => 'string',
            'dpos_names'                        => 'string',
            'people_names'                      => 'string',
            'type'                              => 'string',
        ];

        $this->mergeFromRequest($pia, $updatableAttributes, $request);

        $this->update($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Treatment")
     *
     * @FOSRest\Delete("/pias/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete a treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Pia::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_DELETE_PIA')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $pia = $this->getRepository()->find($id);
        $this->canAccessResourceOr403($pia);
        $this->remove($pia);

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias/import")
     * @Security("is_granted('CAN_IMPORT_PIA')")
     *
     * @return array
     */
    public function importAction(Request $request)
    {
        $importData = $request->get('data', null);
        if ($importData === null) {
            return $this->view($importData, Response::HTTP_BAD_REQUEST);
        }

        $pia = $this->jsonToEntityTransformer->transform($importData);
        $pia->setStructure($this->getUser()->getStructure());
        $pia->setFolder($this->getUser()->getStructure()->getRootFolder());
        $this->persist($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/pias/export/{id}", requirements={"id"="\d+"})
     * @Security("is_granted('CAN_EXPORT_PIA')")
     *
     * @return array
     */
    public function exportAction(Request $request, $id)
    {
        $this->canAccessRouteOr403();

        $pia = $this->getRepository()->find($id);
        $this->canAccessResourceOr403($pia);

        $serializedPia = $this->jsonToEntityTransformer->reverseTransform($pia);

        return new Response($serializedPia, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Pia::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if (!$resource instanceof Pia) {
            throw new AccessDeniedHttpException();
        }

        if ($resource->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
