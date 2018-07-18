<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Structure\Type;

use PiaApi\Repository\StructureTypeRepository;
use PiaApi\Form\Type\EntitySearchChoiceType;

class StructureTypeChoiceType extends EntitySearchChoiceType
{
    public function __construct(StructureTypeRepository $repository)
    {
        parent::__construct($repository);
    }
}
