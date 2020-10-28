<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\Application\Type;

use PiaApi\Repository\ApplicationRepository;
use PiaApi\Form\Type\EntitySearchChoiceType;

class ApplicationChoiceType extends EntitySearchChoiceType
{
    public function __construct(ApplicationRepository $repository)
    {
        parent::__construct($repository);
    }
}
