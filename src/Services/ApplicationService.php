<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use PiaApi\Entity\Oauth\Client;
use FOS\OAuthServerBundle\Model\ClientInterface;

class ApplicationService
{
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    /**
     * @param string     $name
     * @param string     $url
     * @param array|null $grantTypes
     *
     * @return Client
     */
    public function createApplication(string $name, string $url, ?array $grantTypes = ['password', 'token', 'refresh_token']): Client
    {
        /** @var Client $application */
        $application = $this->clientManager->createClient();

        $application->setName($name);
        $application->setUrl($url);
        $application->setAllowedGrantTypes($grantTypes);

        return $application;
    }

    /**
     * @param string     $name
     * @param string     $url
     * @param array|null $grantTypes
     * @param string     $clientId
     * @param string     $clientSecret
     *
     * @return Client
     */
    public function createApplicationWithIdentifiers(string $name, string $url, ?array $grantTypes = ['password', 'token', 'refresh_token'], string $clientId, string $clientSecret): Client
    {
        $application = $this->createApplication($name, $url, $grantTypes);

        $application->setRandomId($clientId);
        $application->setSecret($clientSecret);

        return $application;
    }

    /**
     * @param ClientInterface $application
     */
    public function updateApplication(ClientInterface $application): void
    {
        $this->clientManager->updateClient($application);
    }
}
