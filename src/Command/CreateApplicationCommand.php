<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Command;

use PiaApi\Service\ApplicationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateApplicationCommand extends Command
{
    const NAME = 'pia:application:create';

    /**
     * @var ApplicationService
     */
    private $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        parent::__construct();

        $this->applicationService = $applicationService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::NAME)
            ->setDescription('Creates a new application')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Application\'s name',
                null
            )
            ->addOption(
                'url',
                null,
                InputOption::VALUE_REQUIRED,
                'Application\'s url.',
                null
            )
            ->addOption(
                'client-id',
                null,
                InputOption::VALUE_OPTIONAL,
                'Application\'s client_id.',
                null
            )
            ->addOption(
                'client-secret',
                null,
                InputOption::VALUE_OPTIONAL,
                'Application\'s client_secret.',
                null
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new application.

<info>php %command.full_name% [--name=...] [--url=...] [--client-id=...] [--client-secret=...]</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Client Credentials');

        $application = null;

        $applicationName = $input->getOption('name');
        $applicationUrl = $input->getOption('url');
        $applicationGrantTypes = ['password', 'token', 'refresh_token'];
        $clientId = $input->getOption('client-id', false);
        $clientSecret = $input->getOption('client-secret', false);

        if ($clientId && $clientSecret) {
            $application = $this->applicationService->newApplication(
                $applicationName,
                $applicationUrl,
                $applicationGrantTypes,
                $clientId,
                $clientSecret
            );
        }
        //one of them is set but not the other
        elseif ($clientId xor $clientSecret) {
            $io->error('You must set client_id AND client_secret');

            return;
        } else {
            $application = $this->applicationService->newApplication(
                $applicationName,
                $applicationUrl,
                $applicationGrantTypes
            );
        }

        // Save the client
        $this->applicationService->updateApplication($application);

        // Give the credentials back to the user
        $headers = ['Client ID', 'Client Secret'];
        $rows = [
            [$application->getPublicId(), $application->getSecret()],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}
