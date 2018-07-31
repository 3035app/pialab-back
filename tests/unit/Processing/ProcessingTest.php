<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use PiaApi\Entity\Pia\Folder;
use PiaApi\Entity\Pia\Pia;
use PiaApi\Entity\Pia\Processing;
use PiaApi\Entity\Pia\ProcessingDataType;
use PiaApi\Tests\unit\Processing\_FixturesTrait;

class ProcessingTest extends \Codeception\Test\Unit
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

        $this->assertEquals($this->data['processing']['name'], $processing->getName());
    }

    public function test_create_failed_without_name()
    {
        $this->expectException(\ArgumentCountError::class);

        $processing = new Processing();
    }

    public function test_add_pia()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $pia = new Pia();
        $pia->setName($this->data['pia']['name']);

        $processing->addPia($pia);

        $this->assertContains($pia, $processing->getPias());
    }

    public function test_add_pia_twice_should_throw_exception()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $pia = new Pia();
        $pia->setName($this->data['pia']['name']);

        $processing->addPia($pia);

        $this->assertContains($pia, $processing->getPias());

        $this->expectException(\InvalidArgumentException::class);

        $processing->addPia($pia);
    }

    public function test_add_processed_data_type()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $processedDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processedDataType);

        $this->assertContains($processedDataType, $processing->getProcessingDataTypes());
    }

    public function test_remove_processed_data_type()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $processedDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processedDataType);

        $this->assertContains($processedDataType, $processing->getProcessingDataTypes());

        $processing->removeProcessingDataType($processedDataType);

        $this->assertNotContains($processedDataType, $processing->getProcessingDataTypes());
    }

    public function test_add_processed_data_type_twice_should_throw_exception()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $processedDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processedDataType);

        $this->assertContains($processedDataType, $processing->getProcessingDataTypes());

        $this->expectException(\InvalidArgumentException::class);

        $processing->addProcessingDataType($processedDataType);
    }

    public function test_remove_inexistant_processed_data_type_should_throw_exception()
    {
        $folder = new Folder($this->data['folder']['name']);
        $processing = new Processing($this->data['processing']['name'], $folder);
        $processedDataType = new ProcessingDataType($processing);

        $this->expectException(\InvalidArgumentException::class);

        $processing->removeProcessingDataType($processedDataType);
    }
}
