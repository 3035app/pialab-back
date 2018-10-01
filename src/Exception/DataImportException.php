<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class DataImportException extends HttpException
{
    public function __construct(string $message = null, ?int $code = 0)
    {
        parent::__construct(Response::HTTP_PRECONDITION_FAILED, $message, null, [], $code);
    }
}
