<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Service;

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

    /**
     * @param string             $name
     * @param StructureType|null $structureType
     *
     * @return Structure
     */
    public function newStructure(string $name, ?StructureType $structureType = null): Structure
    {
        $structure = new Structure($name);

        $this->folderService->newFolder('root', $structure);

        if ($structureType !== null) {
            $structure->setType($structureType);
        }

        return $structure;
    }
}
