<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Services\ProcessingService;
use PiaApi\Entity\Pia\Processing;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ProcessingController extends RestController
{
    /**
     * @var ProcessingService
     */
    private $processingService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        ProcessingService $processingService
    ) {
        parent::__construct($propertyAccessor);
        $this->processingService = $processingService;
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
}
