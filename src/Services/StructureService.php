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

    public function createStructure(string $name): Structure
    {
        $structure = new Structure($name);

        $this->folderService->createFolderForStructure('root', $structure);

        return $structure;
    }

    public function createStructureOfType(string $name, ?StructureType $structureType): Structure
    {
        $structure = $this->createStructure($name);
        if ($structureType !== null) {
            $structure->setType($structureType);
        }

        return $structure;
    }
}
