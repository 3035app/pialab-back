<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Portfolio\Type;

use PiaApi\Repository\PortfolioRepository;
use PiaApi\Form\Type\EntitySearchChoiceType;

class PortfolioChoiceType extends EntitySearchChoiceType
{
    public function __construct(PortfolioRepository $repository)
    {
        parent::__construct($repository);
    }
}
