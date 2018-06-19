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
use FOS\RestBundle\View\View;
use PiaApi\Command\ImportPiaTemplatesCommand;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Entity\Pia\PiaTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Output\NullOutput;

class PiaTemplateController extends RestController
{
    /**
     * @var JsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;

    public function __construct(JsonToEntityTransformer $jsonToEntityTransformer)
    {
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
    }

    /**
     * @FOSRest\Get("/pia-templates")
     * @Security("is_granted('CAN_SHOW_PIA_TEMPLATE')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $structure = $this->getUser()->getStructure();
        if ($structure !== null) {
            $collection = $this->getRepository()->findAvailablePiaTemplatesForStructure($structure);
        } elseif ($this->isGranted('ROLE_TECHNICAL_ADMIN')) {
            $collection = $this->getRepository()->findAll();
        } else {
            $collection = [];
        }

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/pia-templates/{id}")
     * @Security("is_granted('CAN_SHOW_PIA_TEMPLATE')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        $piaTemplate = $this->getRepository()->find($id);
        if ($piaTemplate === null) {
            return $this->view($piaTemplate, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($piaTemplate);

        return $this->view($piaTemplate, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pia-templates/importCollection")
     * @Security("is_granted('CAN_CREATE_PIA_TEMPLATE')")
     *
     * @return View
     */
    public function importCollectionAction(Request $request, KernelInterface $kernel): View
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('collection');

        if ($uploadedFile === null) {
            return $this->view('Please send a valid archive', Response::HTTP_BAD_REQUEST);
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $inputData = [
            'command'                  => ImportPiaTemplatesCommand::NAME,
            'templatesFolderOrArchive' => $uploadedFile->getRealPath(),
            '--no-interaction'         => true,
        ];

        if ($request->get('enableAll') !== null) {
            $inputData['--enableAll'] = true;
        }

        $input = new ArrayInput($inputData);

        $output = new NullOutput();
        $returnCode = $application->run($input, $output);

        return $this->view($returnCode, $returnCode === 0 ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function getEntityClass()
    {
        return PiaTemplate::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if (!$resource instanceof PiaTemplate) {
            throw new AccessDeniedHttpException();
        }

        if ($resource->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
