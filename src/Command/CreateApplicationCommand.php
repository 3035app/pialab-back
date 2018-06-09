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
            ->setName('pia:application:create')
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

        // Create a new client
        $client = $this->clientManager->createClient();

        $client->setName($input->getOption('name'));
        $client->setUrl($input->getOption('url'));
        $client->setAllowedGrantTypes(['password', 'token', 'refresh_token']);

        $clientId = $input->getOption('client-id', false);
        $clientSecret = $input->getOption('client-secret', false);

        if ($clientId && $clientSecret) {
            $client->setRandomId($clientId);
            $client->setSecret($clientSecret);
        }
        //one of them is set but not the other
        if (!(!$clientId && !$clientSecret)) {
            $io->error('You must set client_id AND client_secret');

            return;
        }

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
