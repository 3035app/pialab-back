<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Pia;

abstract class AbstractStatus
{
    public const UNKNOWN = -1;

    protected const STATUS_NAME = [
        self::UNKNOWN             => 'UNKNOWN',
    ];

    protected static $statusNames = [];

    protected static function getNames()
    {
        $class = get_called_class();

        return self::STATUS_NAME + $class::$statusNames;
    }

    public static function isValid(int $status): bool
    {
        return isset(self::getNames()[$status]);
    }

    public static function getStatusName(int $status): string
    {
        if (!self::isValid($status)) {
            $status = self::UNKNOWN;
        }

        return self::getNames()[$status];
    }

    /**
     * @return int
     */
    public static function getStatusFromName(string $name): int
    {
        $status = self::UNKNOWN;
        $status_collection = array_flip(self::getNames());

        if (array_key_exists($name, $status_collection)) {
            $status = $status_collection[$name];
        }

        return $status;
    }
}
