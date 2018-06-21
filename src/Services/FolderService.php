<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Folder;
use PiaApi\Entity\Pia\Structure;

class FolderService extends AbstractService
{
    public function getEntityClass(): string
    {
        return Folder::class;
    }

    public function createFolder(string $name): Folder
    {
        return new Folder($name);
    }

    public function createFolderForStructure(string $name, Structure $structure): Folder
    {
        return new Folder($name, $structure);
    }

    public function createFolderForStructureAndParent(string $name, Structure $structure, ?Folder $parent = null): Folder
    {
        $folder = new Folder($name, $structure);

        if ($parent !== null) {
            $folder->setParent($parent);
        }

        return $folder;
    }

    public function createFolderForParent(string $name, Folder $parent): Folder
    {
        $folder = new Folder($name);
        $folder->setParent($parent);

        return $folder;
    }
}
