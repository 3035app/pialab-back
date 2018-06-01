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
use PiaApi\Form\PiaTemplate\CreatePiaTemplateForm;
use PiaApi\Form\PiaTemplate\EditPiaTemplateForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Entity\Pia\PiaTemplate;
use PiaApi\Form\PiaTemplate\RemovePiaTemplateForm;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PiaTemplateController extends BackOfficeAbstractController
{
    /**
     * @Route("/managePiaTemplates", name="manage_pia_templates")
     */
    public function managePiaTemplatesAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $this->canAccess();

        $pagerfanta = $this->buildPager($request, PiaTemplate::class);

        return $this->render('pia/PiaTemplate/managePiaTemplates.html.twig', [
            'piaTemplates'     => $pagerfanta,
        ]);
    }

    /**
     * @Route("/managePiaTemplates/addPiaTemplate", name="manage_pia_templates_add_pia_template")
     *
     * @param Request $request
     */
    public function addPiaTemplateAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreatePiaTemplateForm::class, [], [
            'action' => $this->generateUrl('manage_pia_templates_add_pia_template'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $piaTemplateData = $form->getData();

            $piaTemplate = new PiaTemplate($piaTemplateData['name']);

            /** @var UploadedFile $file */
            $file = $piaTemplateData['data'];

            if ($file) {
                $piaTemplate->addFile($file);
            }
            if (isset($piaTemplateData['description'])) {
                $piaTemplate->setDescription($piaTemplateData['description']);
            }

            $this->getDoctrine()->getManager()->persist($piaTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_pia_templates'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/managePiaTemplates/editPiaTemplate/{piaTemplateId}", name="manage_pia_templates_edit_pia_template")
     *
     * @param Request $request
     */
    public function editPiaTemplateAction(Request $request)
    {
        $this->canAccess();

        $piaTemplateId = $request->get('piaTemplateId');
        $piaTemplate = $this->getDoctrine()->getRepository(PiaTemplate::class)->find($piaTemplateId);

        if ($piaTemplate === null) {
            throw new NotFoundHttpException(sprintf('PiaTemplate « %s » does not exist', $piaTemplateId));
        }

        $form = $this->createForm(EditPiaTemplateForm::class, $piaTemplate, [
            'action' => $this->generateUrl('manage_pia_templates_edit_pia_template', ['piaTemplateId' => $piaTemplate->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $piaTemplate = $form->getData();

            $hasFile = $request->files->get('edit_pia_template_form');

            if ($hasFile && $file = $hasFile['newData']) {
                $piaTemplate->addFile($file);
            }

            $this->getDoctrine()->getManager()->persist($piaTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_pia_templates'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/managePiaTemplates/removePiaTemplate/{piaTemplateId}", name="manage_pia_templates_remove_pia_template")
     *
     * @param Request $request
     */
    public function removePiaTemplateAction(Request $request)
    {
        $this->canAccess();

        $piaTemplateId = $request->get('piaTemplateId');
        $piaTemplate = $this->getDoctrine()->getRepository(PiaTemplate::class)->find($piaTemplateId);

        if ($piaTemplate === null) {
            throw new NotFoundHttpException(sprintf('PiaTemplate « %s » does not exist', $piaTemplateId));
        }

        $form = $this->createForm(RemovePiaTemplateForm::class, $piaTemplate, [
            'action' => $this->generateUrl('manage_pia_templates_remove_pia_template', ['piaTemplateId' => $piaTemplate->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $piaTemplate = $form->getData();

            $this->getDoctrine()->getManager()->remove($piaTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_pia_templates'));
        }

        return $this->render('pia/PiaTemplate/removePiaTemplate.html.twig', [
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
