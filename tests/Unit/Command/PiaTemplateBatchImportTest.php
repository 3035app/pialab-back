<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Tests\Unit\Command;

use PiaApi\Command\PiaTemplatesBatchImportCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PiaTemplateBatchImportTest extends WebTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $di = $kernel->getContainer();

        $application = new Application($kernel);

        $application->add(new PiaTemplatesBatchImportCommand(
            $di->get('doctrine')->getManager(),
            $di->get('JsonToEntityTransformer'),
            $di->get('PiaTemplateService')
        ));

        $command = $application->find(PiaTemplatesBatchImportCommand::NAME);

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'          => $command->getName(),
            'path'             => __DIR__ . '/fixtures',
            '--dry-run'        => null,
            '--no-interaction' => null,
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK]  1 templates imported', $output);
    }
}
