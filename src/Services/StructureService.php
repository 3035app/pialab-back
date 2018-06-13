<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\StructureType;

class StructureService
{
    /**
     * @var FolderService
     */
    private $folderService;

    public function __construct(FolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    public function createStructure(string $name): Structure
    {
        $structure = new Structure($name);

        $this->folderService->createFolderForStructure('root', $structure);

        return $structure;
    }

    public function createStructureOfType(string $name, StructureType $structureType): Structure
    {
        $structure = $this->createStructure($name);
        $structure->setType($structureType);

        return $structure;
    }
}
