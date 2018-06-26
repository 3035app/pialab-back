<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\StructureType;

class StructureTypeService
{
    public function getEntityClass(): string
    {
        return StructureType::class;
    }

    /**
     * @param string $name
     *
     * @return StructureType
     */
    public function createStructureType(string $name): StructureType
    {
        return new StructureType($name);
    }
}
