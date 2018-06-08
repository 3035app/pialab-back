<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;

class CreateApplicationCommand extends Command
{
    private $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();

        $this->clientManager = $clientManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('pia:create-application')
            ->setDescription('Creates a new application')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name dor application',
                null
            )
            ->addOption(
                'url',
                null,
                InputOption::VALUE_REQUIRED,
                'Url for application.',
                null
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new application.

<info>php %command.full_name% [--name=...] [--url=...]</info>

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

        // Create a new client
        $client = $this->clientManager->createClient();

        $client->setName($input->getOption('name'));
        $client->setUrl($input->getOption('url'));
        $client->setAllowedGrantTypes(['password', 'token', 'refresh_token']);

        // Save the client
        $this->clientManager->updateClient($client);

        // Give the credentials back to the user
        $headers = ['Client ID', 'Client Secret'];
        $rows = [
            [$client->getPublicId(), $client->getSecret()],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}
