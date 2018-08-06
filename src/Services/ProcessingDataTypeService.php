<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\ProcessingDataType;

class ProcessingDataTypeService extends AbstractService
{
    public function getEntityClass(): string
    {
        return ProcessingDataType::class;
    }

    /**
     * 
     * @param string    $name
     * 
     * @return ProcessingDataType
     */
    public function createProcessingDataType(Processing $processing, string $reference): ProcessingDataType
    {
        $processingDataType = new ProcessingDataType($processing, $reference);
        
        return $processingDataType;
    }
}
