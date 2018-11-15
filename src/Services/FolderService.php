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

    /**
     * @param string         $name
     * @param Structure|null $structure
     * @param Folder|null    $parent
     *
     * @return Folder
     */
    public function createFolder(string $name, ?Structure $structure = null, ?Folder $parent = null, ?string $personInCharge = null): Folder
    {
        $folder = new Folder($name, $structure);

        if ($parent !== null) {
            $folder->setParent($parent);
        }

        if($personInCharge !== null) {
            $folder->setPersonInCharge($personInCharge);
        }

        return $folder;
    }
}
