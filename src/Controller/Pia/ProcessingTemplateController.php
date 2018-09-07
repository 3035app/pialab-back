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
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Command\ImportProcessingTemplatesCommand;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Entity\Pia\ProcessingTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ProcessingTemplateController extends RestController
{
    /**
     * @var JsonToEntityTransformer
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
     * Lists all ProcessingTemplates, for a Structure if User is not a Technical admin.
     *
     * @Swg\Tag(name="ProcessingTemplate")
     *
     * @FOSRest\Get("/processing-templates")
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
     *     description="Returns all ProcessingTemplates",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=ProcessingTemplate::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING_TEMPLATE')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $structure = $this->getUser()->getStructure();
        if ($structure !== null) {
            $collection = $this->getRepository()->findAvailableProcessingTemplatesForStructure($structure);
        } elseif ($this->isGranted('ROLE_TECHNICAL_ADMIN')) {
            $collection = $this->getRepository()->findAll();
        } else {
            $collection = [];
        }

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one ProcessingTemplate by its ID.
     *
     * @Swg\Tag(name="ProcessingTemplate")
     *
     * @FOSRest\Get("/processing-templates/{id}")
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
     *     description="The ID of the ProcessingTemplate"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one ProcessingTemplate",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=ProcessingTemplate::class, groups={"Default"})
     *     )
     * )
     * @Swg\Response(
     *     response=404,
     *     description="ProcessingTemplate does not exists"
     * )
     *
     * @Security("is_granted('CAN_SHOW_PROCESSING_TEMPLATE')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        $pTemplate = $this->getRepository()->find($id);
        if ($pTemplate === null) {
            return $this->view($pTemplate, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($pTemplate);

        return $this->view($pTemplate, Response::HTTP_OK);
    }

    /**
     * Imports ProcessingTemplates contained in a given archive.
     *
     * @Swg\Tag(name="ProcessingTemplate")
     *
     * @FOSRest\Post("/processing-templates/importCollection")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="enableAll",
     *     in="formData",
     *     type="boolean",
     *     required=true,
     *     description="Auto enable all templates"
     * )
     * @Swg\Parameter(
     *     name="collection",
     *     in="formData",
     *     type="file",
     *     required=true,
     *     description="The archive of ProcessingTemplates"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content, Import ProcessingTemplates is OK"
     * )
     * @Swg\Response(
     *     response=500,
     *     description="Empty content, Import ProcessingTemplates fails"
     * )
     *
     * @Security("is_granted('CAN_CREATE_PROCESSING_TEMPLATE')")
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
            'command'                  => ImportProcessingTemplatesCommand::NAME,
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
        return ProcessingTemplate::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if (!$resource instanceof ProcessingTemplate) {
            throw new AccessDeniedHttpException();
        }

        if (!$resource->getStructures()->contains($this->getUser()->getStructure())) {
            throw new AccessDeniedHttpException();
        }
    }
}
