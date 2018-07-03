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

class RootFolderCannotBeDeletedException extends ApiException
{
    public function __construct()
    {
        $message = 'You cannot delete Root folder';
        parent::__construct(Response::HTTP_CONFLICT, $message, 702);
    }
}
