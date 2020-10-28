<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Tests\functionnal;

use PiaApi\Command\ProcessingTemplatesBatchImportCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use PiaApi\Kernel;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Services\ProcessingTemplateService;

class ProcessingTemplateBatchImportTest extends WebTestCase
{
    /**
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    public function testExecute()
    {
        $kernel = self::bootKernel();

        $di = self::$container;

        $application = new Application($kernel);

        $application->add(
            new ProcessingTemplatesBatchImportCommand(
                $di->get('doctrine')->getManager(),
                $di->get(JsonToEntityTransformer::class),
                $di->get(ProcessingTemplateService::class)
            )
        );

        $command = $application->find(ProcessingTemplatesBatchImportCommand::NAME);

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'          => $command->getName(),
            'path'             => __DIR__ . '/fixtures',
            '--dry-run'        => null,
            '--no-interaction' => null,
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK]  1 templates imported', $output);
    }
}
