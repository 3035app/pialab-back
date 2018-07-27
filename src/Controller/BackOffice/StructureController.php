<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\StructureType;
use PiaApi\Entity\Oauth\User;
use PiaApi\Form\Structure\CreateStructureForm;
use PiaApi\Form\Structure\CreateStructureTypeForm;
use PiaApi\Form\Structure\EditStructureForm;
use PiaApi\Form\Structure\EditStructureTypeForm;
use PiaApi\Form\Structure\RemoveStructureForm;
use PiaApi\Form\Structure\RemoveStructureTypeForm;
use PiaApi\Form\User\CreateUserForm;
use PiaApi\Services\StructureService;
use PiaApi\Services\StructureTypeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StructureController extends BackOfficeAbstractController
{
    /**
     * @var StructureService
     */
    private $structureService;

    /**
     * @var StructureTypeService
     */
    private $structureTypeService;

    public function __construct(StructureService $structureService, StructureTypeService $structureTypeService)
    {
        $this->structureService = $structureService;
        $this->structureTypeService = $structureTypeService;
    }

    /**
     * @Route("/manageStructures", name="manage_structures")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_SHOW_STRUCTURE')")
     */
    public function manageStructuresAction(Request $request)
    {
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES')) {
            $portfolios = $this->getUser()->getPortfolios();

            $pagerFanta = $this->getDoctrine()
                ->getRepository(Structure::class)
                ->getPaginatedStructuresForPortfolios($portfolios);

            $page = $request->get('page', 1);
            $limit = $request->get('limit', $pagerFanta->getMaxPerPage());

            $pagerFanta->setMaxPerPage($limit);
            $pagerFanta->setCurrentPage($pagerFanta->getNbPages() < $page ? $pagerFanta->getNbPages() : $page);
            $pagerFantaSt = null;
        } else {
            $pagerFanta = $this->buildPager($request, Structure::class);
            $pagerFantaSt = $this->buildPager($request, StructureType::class, 20, 'pageSt', 'limitSt');
        }

        return $this->render('pia/Structure/manageStructures.html.twig', [
            'structures'     => $pagerFanta,
            'structureTypes' => $pagerFantaSt,
        ]);
    }

    /**
     * @Route("/showStructure/{structureId}", name="manage_structures_show_structure")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_SHOW_STRUCTURE')")
     */
    public function showStructureAction(Request $request)
    {
        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $userPager = $this->getDoctrine()
          ->getRepository(User::class)
          ->getPaginatedUsersByStructure($structure);

        $userPage = $request->get('page', 1);
        $userLimit = $request->get('limit', $userPager->getMaxPerPage());

        $userPager->setMaxPerPage($userLimit);
        $userPager->setCurrentPage($userPager->getNbPages() < $userPage ? $userPager->getNbPages() : $userPage);

        $userForm = $this->createForm(CreateUserForm::class, ['roles' => ['ROLE_USER']], [
            'action'      => $this->generateUrl('manage_users_add_user'),
            'structure'   => $structure,
            'redirect'    => $this->generateUrl('manage_structures_show_structure', ['structureId' => $structureId]),
        ]);

        return $this->render('pia/Structure/showStructure.html.twig', [
            'structure'     => $structure,
            'users'         => $userPager,
            'userForm'      => $userForm->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/addStructure", name="manage_structures_add_structure")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_CREATE_STRUCTURE')")
     *
     * @param Request $request
     */
    public function addStructureAction(Request $request)
    {
        $form = $this->createForm(CreateStructureForm::class, [], [
            'action'   => $this->generateUrl('manage_structures_add_structure'),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureData = array_merge([
                'name'      => null,
                'type'      => null,
                'portfolio' => null,
            ], $form->getData());

            $structure = $this->structureService->createStructure(
                $structureData['name'],
                $structureData['type'],
                $structureData['portfolio']
            );

            $this->getDoctrine()->getManager()->persist($structure);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/addStructureType", name="manage_structures_add_structure_type")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_CREATE_STRUCTURE_TYPE')")
     *
     * @param Request $request
     */
    public function addStructureTypeAction(Request $request)
    {
        $form = $this->createForm(CreateStructureTypeForm::class, [], [
            'action'   => $this->generateUrl('manage_structures_add_structure_type'),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureTypeData = $form->getData();

            $structureType = $this->structureTypeService->createStructureType($structureTypeData['name']);

            $this->getDoctrine()->getManager()->persist($structureType);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/editStructure/{structureId}", name="manage_structures_edit_structure")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_EDIT_STRUCTURE')")
     *
     * @param Request $request
     */
    public function editStructureAction(Request $request)
    {
        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $form = $this->createForm(EditStructureForm::class, $structure, [
            'action'   => $this->generateUrl('manage_structures_edit_structure', ['structureId' => $structure->getId()]),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure = $form->getData();
            $this->getDoctrine()->getManager()->persist($structure);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/editStructureType/{structureTypeId}", name="manage_structures_edit_structure_type")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_EDIT_STRUCTURE_TYPE')")
     *
     * @param Request $request
     */
    public function editStructureTypeAction(Request $request)
    {
        $structureTypeId = $request->get('structureTypeId');
        $structureType = $this->getDoctrine()->getRepository(StructureType::class)->find($structureTypeId);

        if ($structureType === null) {
            throw new NotFoundHttpException(sprintf('StructureType « %s » does not exist', $structureTypeId));
        }

        $form = $this->createForm(EditStructureTypeForm::class, $structureType, [
            'action'   => $this->generateUrl('manage_structures_edit_structure_type', ['structureTypeId' => $structureType->getId()]),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureType = $form->getData();

            $this->getDoctrine()->getManager()->persist($structureType);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/removeStructure/{structureId}", name="manage_structures_remove_structure")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_DELETE_STRUCTURE')")
     *
     * @param Request $request
     */
    public function removeStructureAction(Request $request)
    {
        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $form = $this->createForm(RemoveStructureForm::class, $structure, [
            'action'   => $this->generateUrl('manage_structures_remove_structure', ['structureId' => $structure->getId()]),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure = $form->getData();

            foreach ($structure->getUsers() as $user) {
                $user->setStructure(null);
            }

            $this->getDoctrine()->getManager()->remove($structure);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Structure/removeStructure.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/removeStructureType/{structureTypeId}", name="manage_structures_remove_structure_type")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_DELETE_STRUCTURE_TYPE')")
     *
     * @param Request $request
     */
    public function removeStructureTypeAction(Request $request)
    {
        $structureTypeId = $request->get('structureTypeId');
        $structureType = $this->getDoctrine()->getRepository(StructureType::class)->find($structureTypeId);

        if ($structureType === null) {
            throw new NotFoundHttpException(sprintf('StructureType « %s » does not exist', $structureTypeId));
        }

        $form = $this->createForm(RemoveStructureTypeForm::class, $structureType, [
            'action'   => $this->generateUrl('manage_structures_remove_structure_type', ['structureTypeId' => $structureType->getId()]),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureType = $form->getData();

            foreach ($structureType->getStructures() as $structure) {
                $structure->setType(null);
            }

            $this->getDoctrine()->getManager()->remove($structureType);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Structure/removeStructureType.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/dissociateStructure/{structureId}", name="manage_structures_dissociate_structure")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_EDIT_STRUCTURE')")
     *
     * @param Request $request
     */
    public function dissociateStructureAction(Request $request)
    {
        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }
        $structure->setPortfolio(null);
        $this->getDoctrine()->getManager()->persist($structure);
        $this->getDoctrine()->getManager()->flush();

        $customRedirect = $this->getQueryRedirectUrl($request);
        $redirectUrl = $customRedirect ?? $this->generateUrl('manage_structures');

        return $this->redirect($redirectUrl);
    }
}
