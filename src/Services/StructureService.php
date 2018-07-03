<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\StructureType;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StructureService extends AbstractService
{
    /**
     * @var FolderService
     */
    private $folderService;

    public function __construct(
        RegistryInterface $doctrine,
        FolderService $folderService
    ) {
        parent::__construct($doctrine);
        $this->folderService = $folderService;
    }

    public function getEntityClass(): string
    {
        return Structure::class;
    }

    /**
     * @param string             $name
     * @param StructureType|null $structureType
     *
     * @return Structure
     */
    public function createStructure(string $name, ?StructureType $structureType = null): Structure
    {
        $structure = new Structure($name);

        $this->folderService->createFolder('root', $structure);

        if ($structureType !== null) {
            $structure->setType($structureType);
        }

        return $structure;
    }
}
