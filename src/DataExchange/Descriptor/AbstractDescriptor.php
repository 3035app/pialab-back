<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\DataExchange\Descriptor;

class AbstractDescriptor
{
    protected function checkProperty($mode, $method)
    {
        $attribute = false;
        $reflect = new \ReflectionClass($this);

        $name = lcfirst(str_replace($mode, '', $method));
        $properties = array_column($reflect->getProperties(), 'name');

        if (in_array($name, $properties)) {
            $attribute = $name;
        }

        return $attribute;
    }

    public function __call($method, $args)
    {
        $value = false;

        if (strpos($method, 'set') !== false) {
            if ($name = $this->checkProperty('set', $method)) {
                $this->$name = $args[0];
            }

            $value = true;
        }

        if (strpos($method, 'get') !== false) {
            if ($name = $this->checkProperty('get', $method)) {
                $value = $this->$name;
            }
        }

        return $value;
    }
}
