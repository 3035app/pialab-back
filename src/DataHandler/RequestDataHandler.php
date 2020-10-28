<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataHandler;

class RequestDataHandler
{
    const TYPE_STRING = 'string';
    const TYPE_NULLABLE_STRING = 'null|string';
    const TYPE_INT = 'int';
    const TYPE_NULLABLE_INT = 'null|int';
    const TYPE_BOOL = 'bool';
    const TYPE_NULLABLE_BOOL = 'null|bool';
    const TYPE_ARRAY = 'array';
    const TYPE_NULLABLE_ARRAY = 'null|array';

    /**
     * The data representation issued from request.
     *
     * @var mixed
     */
    private $rawData;

    /**
     * The targeted data type.
     *
     * @var string
     */
    private $targetType;

    public function __construct($rawData, string $targetType)
    {
        $this->rawData = $rawData;
        $this->targetType = $targetType;
    }

    public function getValue()
    {
        switch ($this->targetType) {
            case self::TYPE_STRING:
                return (string) $this->rawData;
                break;
            case self::TYPE_NULLABLE_STRING:
                return $this->rawData === null ? null : (string) $this->rawData;
                break;
            case self::TYPE_INT:
                return (int) $this->rawData;
                break;
            case self::TYPE_NULLABLE_INT:
                return $this->rawData === null ? null : (int) $this->rawData;
                break;
            case self::TYPE_BOOL:
                return boolval($this->rawData);
                break;
            case self::TYPE_NULLABLE_BOOL:
                return $this->rawData === null ? null : boolval($this->rawData);
                break;
            case self::TYPE_ARRAY:
                return (array) $this->rawData;
                break;
            case self::TYPE_NULLABLE_ARRAY:
                return $this->rawData === null ? null : (array) $this->rawData;
                break;
            default:
                return $this->rawData === null ? null : new $this->targetType($this->rawData);
        }
    }
}
