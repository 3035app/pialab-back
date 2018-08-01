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

    public function getProcessing(): Processing
    {
        $folder = new Folder($this->data['folder']['name']);

        return new Processing(
            $this->data['processing']['name'],
            $folder,
            $this->data['processing']['author'],
            $this->data['processing']['processors'],
            $this->data['processing']['controllers']
        );
    }

    public function test_create()
    {
        $processing = $this->getProcessing();

        $this->assertEquals($this->data['processing']['name'], $processing->getName());
    }

    public function test_create_failed_without_name()
    {
        $this->expectException(\ArgumentCountError::class);

        $processing = new Processing();
    }

    public function test_add_pia()
    {
        $processing = $this->getProcessing();
        $pia = new Pia();
        $pia->setName($this->data['pia']['name']);

        $processing->addPia($pia);

        $this->assertContains($pia, $processing->getPias());
    }

    public function test_add_pia_twice_should_throw_exception()
    {
        $processing = $this->getProcessing();
        $pia = new Pia();
        $pia->setName($this->data['pia']['name']);

        $processing->addPia($pia);

        $this->assertContains($pia, $processing->getPias());

        $this->expectException(\InvalidArgumentException::class);

        $processing->addPia($pia);
    }

    public function test_add_processing_data_type()
    {
        $processing = $this->getProcessing();
        $processingDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processingDataType);

        $this->assertContains($processingDataType, $processing->getProcessingDataTypes());
    }

    public function test_remove_processing_data_type()
    {
        $processing = $this->getProcessing();
        $processingDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processingDataType);

        $this->assertContains($processingDataType, $processing->getProcessingDataTypes());

        $processing->removeProcessingDataType($processingDataType);

        $this->assertNotContains($processingDataType, $processing->getProcessingDataTypes());
    }

    public function test_add_processing_data_type_twice_should_throw_exception()
    {
        $processing = $this->getProcessing();
        $processingDataType = new ProcessingDataType($processing);

        $processing->addProcessingDataType($processingDataType);

        $this->assertContains($processingDataType, $processing->getProcessingDataTypes());

        $this->expectException(\InvalidArgumentException::class);

        $processing->addProcessingDataType($processingDataType);
    }

    public function test_remove_inexistant_processing_data_type_should_throw_exception()
    {
        $processing = $this->getProcessing();
        $processingDataType = new ProcessingDataType($processing);

        $this->expectException(\InvalidArgumentException::class);

        $processing->removeProcessingDataType($processingDataType);
    }

    public function test_set_processing_status_to_archived()
    {
        $processing = $this->getProcessing();
        $processing->setStatus(Processing::STATUS_ARCHIVED);

        $this->assertEquals(Processing::STATUS_ARCHIVED, $processing->getStatus());
    }

    public function test_set_processing_status_to_inexisting_one()
    {
        $processing = $this->getProcessing();

        $this->expectException(\InvalidArgumentException::class);

        $processing->setStatus(-1);
    }
}
