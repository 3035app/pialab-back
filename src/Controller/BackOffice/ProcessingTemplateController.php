<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use PiaApi\Entity\Pia\ProcessingTemplate;
use PiaApi\Form\ProcessingTemplate\CreateProcessingTemplateForm;
use PiaApi\Form\ProcessingTemplate\EditProcessingTemplateForm;
use PiaApi\Form\ProcessingTemplate\RemoveProcessingTemplateForm;
use PiaApi\Services\ProcessingTemplateService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProcessingTemplateController extends BackOfficeAbstractController
{
    /**
     * @var ProcessingTemplateService
     */
    private $pTemplateService;

    public function __construct(
        ProcessingTemplateService $pTemplateService
    ) {
        $this->pTemplateService = $pTemplateService;
    }

    /**
     * @Route("/manageProcessingTemplates", name="manage_processing_templates")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_SHOW_PROCESSING_TEMPLATE')")
     *
     * @param Request $request
     */
    public function manageProcessingTemplatesAction(Request $request)
    {
        $pagerfanta = $this->buildPager($request, ProcessingTemplate::class);

        return $this->render('pia/ProcessingTemplate/manageProcessingTemplates.html.twig', [
            'templates'     => $pagerfanta,
        ]);
    }

    /**
     * @Route("/manageProcessingTemplates/addProcessingTemplate", name="manage_processing_templates_add_processing_template")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_CREATE_PROCESSING_TEMPLATE')")
     *
     * @param Request $request
     */
    public function addProcessingTemplateAction(Request $request)
    {
        $form = $this->createForm(CreateProcessingTemplateForm::class, [], [
            'action' => $this->generateUrl('manage_processing_templates_add_processing_template'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pTemplateData = $form->getData();

            $pTemplate = $this->pTemplateService->createTemplateWithFile(
                $pTemplateData['name'],
                $pTemplateData['data'],
                isset($pTemplateData['description']) ? $pTemplateData['description'] : null
            );

            $this->getDoctrine()->getManager()->persist($pTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_processing_templates'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageProcessingTemplates/editProcessingTemplate/{templateId}", name="manage_processing_templates_edit_processing_template")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_EDIT_PROCESSING_TEMPLATE')")
     *
     * @param Request $request
     */
    public function editProcessingTemplateAction(Request $request)
    {
        $templateId = $request->get('templateId');
        $pTemplate = $this->getDoctrine()->getRepository(ProcessingTemplate::class)->find($templateId);

        if ($pTemplate === null) {
            throw new NotFoundHttpException(sprintf('ProcessingTemplate « %s » does not exist', $templateId));
        }

        $form = $this->createForm(EditProcessingTemplateForm::class, $pTemplate, [
            'action' => $this->generateUrl('manage_processing_templates_edit_processing_template', ['templateId' => $pTemplate->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pTemplate = $form->getData();

            $hasFile = $request->files->get('edit_processing_template_form');

            if ($hasFile && $file = $hasFile['newData']) {
                $pTemplate->addFile($file);
            }

            $this->getDoctrine()->getManager()->persist($pTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_processing_templates'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageProcessingTemplates/removeProcessingTemplate/{templateId}", name="manage_processing_templates_remove_processing_template")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_DELETE_PROCESSING_TEMPLATE')")
     *
     * @param Request $request
     */
    public function removeProcessingTemplateAction(Request $request)
    {
        $templateId = $request->get('templateId');
        $pTemplate = $this->getDoctrine()->getRepository(ProcessingTemplate::class)->find($templateId);

        if ($pTemplate === null) {
            throw new NotFoundHttpException(sprintf('ProcessingTemplate « %s » does not exist', $templateId));
        }

        $form = $this->createForm(RemoveProcessingTemplateForm::class, $pTemplate, [
            'action' => $this->generateUrl('manage_processing_templates_remove_processing_template', ['templateId' => $pTemplate->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pTemplate = $form->getData();

            $this->getDoctrine()->getManager()->remove($pTemplate);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_processing_templates'));
        }

        return $this->render('pia/ProcessingTemplate/removeProcessingTemplate.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
