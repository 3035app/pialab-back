<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

class ProcessingStatus extends AbstractStatus
{
    public const STATUS_DOING = 0;
    public const STATUS_ARCHIVED = 1;

    protected static $statusNames = [
        self::STATUS_DOING      => 'STATUS_DOING',
        self::STATUS_ARCHIVED   => 'STATUS_ARCHIVED',
    ];
}
