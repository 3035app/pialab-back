<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Portfolio;

class PortfolioService
{
    public function getEntityClass(): string
    {
        return Portfolio::class;
    }

    /**
     * @param string $name
     *
     * @return Portfolio
     */
    public function createPortfolio(string $name): Portfolio
    {
        return new Portfolio($name);
    }
}
