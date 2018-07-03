<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Exception\Folder;

use PiaApi\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

class NonEmptyFolderCannotBeDeletedException extends ApiException
{
    public function __construct()
    {
        $message = 'Folder must be empty before being deleted';
        parent::__construct(Response::HTTP_CONFLICT, $message, 701);
    }
}
