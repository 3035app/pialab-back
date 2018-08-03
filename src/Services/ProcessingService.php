<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Folder;
use PiaApi\Entity\Pia\Processing;

class ProcessingService extends AbstractService
{
    public function getEntityClass(): string
    {
        return Processing::class;
    }

    /**
     * 
     * @param string    $name
     * @param Folder    $folder
     * 
     * @return Processing
     */
    public function createProcessing(string $name, Folder $folder): Processing
    {
        $processing = new Processing($name, $folder);


        return $processing;
    }
}
