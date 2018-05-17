<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PiaApi\Form\Structure\CreateStructureForm;
use PiaApi\Form\Structure\EditStructureForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Form\Structure\RemoveStructureForm;
use PiaApi\Form\Structure\CreateStructureTypeForm;
use PiaApi\Entity\Pia\StructureType;
use PiaApi\Form\Structure\EditStructureTypeForm;
use PiaApi\Form\Structure\RemoveStructureTypeForm;

class StructureController extends BackOfficeAbstractController
{
    /**
     * @Route("/manageStructures", name="manage_structures")
     */
    public function manageStructuresAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $this->canAccess();

        $pagerfanta = $this->buildPager($request, Structure::class);
        $pagerfantaSt = $this->buildPager($request, StructureType::class, 20, 'pageSt', 'limitSt');

        return $this->render('pia/Structure/manageStructures.html.twig', [
            'structures'     => $pagerfanta,
            'structureTypes' => $pagerfantaSt,
        ]);
    }

    /**
     * @Route("/manageStructures/addStructure", name="manage_structures_add_structure")
     *
     * @param Request $request
     */
    public function addStructureAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreateStructureForm::class, [], [
            'action' => $this->generateUrl('manage_structures_add_structure'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureData = $form->getData();

            $structure = new Structure($structureData['name']);

            $structure->setType($structureData['type']);

            $this->getDoctrine()->getManager()->persist($structure);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/addStructureType", name="manage_structures_add_structure_type")
     *
     * @param Request $request
     */
    public function addStructureTypeAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreateStructureTypeForm::class, [], [
            'action' => $this->generateUrl('manage_structures_add_structure_type'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureTypeData = $form->getData();

            $structureType = new StructureType($structureTypeData['name']);

            $this->getDoctrine()->getManager()->persist($structureType);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/editStructure/{structureId}", name="manage_structures_edit_structure")
     *
     * @param Request $request
     */
    public function editStructureAction(Request $request)
    {
        $this->canAccess();

        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $form = $this->createForm(EditStructureForm::class, $structure, [
            'action' => $this->generateUrl('manage_structures_edit_structure', ['structureId' => $structure->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure = $form->getData();

            $this->getDoctrine()->getManager()->persist($structure);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/editStructureType/{structureTypeId}", name="manage_structures_edit_structure_type")
     *
     * @param Request $request
     */
    public function editStructureTypeAction(Request $request)
    {
        $this->canAccess();

        $structureTypeId = $request->get('structureTypeId');
        $structureType = $this->getDoctrine()->getRepository(StructureType::class)->find($structureTypeId);

        if ($structureType === null) {
            throw new NotFoundHttpException(sprintf('StructureType « %s » does not exist', $structureTypeId));
        }

        $form = $this->createForm(EditStructureTypeForm::class, $structureType, [
            'action' => $this->generateUrl('manage_structures_edit_structure_type', ['structureTypeId' => $structureType->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureType = $form->getData();

            $this->getDoctrine()->getManager()->persist($structureType);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/removeStructure/{structureId}", name="manage_structures_remove_structure")
     *
     * @param Request $request
     */
    public function removeStructureAction(Request $request)
    {
        $this->canAccess();

        $structureId = $request->get('structureId');
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($structure === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $form = $this->createForm(RemoveStructureForm::class, $structure, [
            'action' => $this->generateUrl('manage_structures_remove_structure', ['structureId' => $structure->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure = $form->getData();

            foreach ($structure->getUsers() as $user) {
                $user->setStructure(null);
            }

            $this->getDoctrine()->getManager()->remove($structure);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Structure/removeStructure.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageStructures/removeStructureType/{structureTypeId}", name="manage_structures_remove_structure_type")
     *
     * @param Request $request
     */
    public function removeStructureTypeAction(Request $request)
    {
        $this->canAccess();

        $structureTypeId = $request->get('structureTypeId');
        $structureType = $this->getDoctrine()->getRepository(StructureType::class)->find($structureTypeId);

        if ($structureType === null) {
            throw new NotFoundHttpException(sprintf('StructureType « %s » does not exist', $structureTypeId));
        }

        $form = $this->createForm(RemoveStructureTypeForm::class, $structureType, [
            'action' => $this->generateUrl('manage_structures_remove_structure_type', ['structureTypeId' => $structureType->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structureType = $form->getData();

            foreach ($structureType->getStructures() as $structure) {
                $structure->setType(null);
            }

            $this->getDoctrine()->getManager()->remove($structureType);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_structures'));
        }

        return $this->render('pia/Structure/removeStructureType.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function canAccess()
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
    }
}
