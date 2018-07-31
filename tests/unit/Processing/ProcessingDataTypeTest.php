<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use PiaApi\Entity\Pia\Folder;
use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\Tests\unit\Processing\_FixturesTrait;

class ProcessingDataTypeTest extends \Codeception\Test\Unit
{
    use _FixturesTrait;

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function test_create()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $processingDataType = new ProcessingDataType($processing);

        $this->assertEquals($processing, $processingDataType->getProcessing());
    }

    public function test_create_failed_without_name()
    {
        $this->expectException(\ArgumentCountError::class);

        $processingDataType = new ProcessingDataType();
    }
}
