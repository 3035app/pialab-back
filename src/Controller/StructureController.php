<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use PiaApi\Form\Structure\CreateStructureForm;
use PiaApi\Form\Structure\EditStructureForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Form\Structure\RemoveStructureForm;

class StructureController extends Controller
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

        $queryBuilder = $this->getDoctrine()->getRepository(Structure::class)->createQueryBuilder('s');

        $queryBuilder
            ->orderBy('s.id', 'DESC');

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $this->render('pia/Structure/manageStructures.html.twig', [
            'structures' => $pagerfanta,
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

            $this->getDoctrine()->getManager()->persist($structure);
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
     * @Route("/manageStructures/removeStructure/{structureId}", name="manage_structures_remove_structure")
     *
     * @param Request $request
     */
    public function removeStructureAction(Request $request)
    {
        $this->canAccess();

        $structureId = $request->get('structureId');
        $user = $this->getDoctrine()->getRepository(Structure::class)->find($structureId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('Structure « %s » does not exist', $structureId));
        }

        $form = $this->createForm(RemoveStructureForm::class, $user, [
            'action' => $this->generateUrl('manage_structures_remove_structure', ['structureId' => $user->getId()]),
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

    protected function canAccess()
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
    }
}
