<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * @var string
     */
    private $baseurl = null;

    /**
     * @var string
     */
    private $clientId = null;

    /**
     * @var string
     */
    private $clientSecret = null;

    /**
     * @var string
     */
    private $user = null;

    /**
     * @var string
     */
    private $password = null;

    /**
     * @var string
     */
    private $token = null;

    /**
     * @var string
     */
    private $refreshToken = null;

    public function getBaseUrl()
    {
        if (!isset($this->baseurl)) {
            if (getenv('TEST_SERVER_URL') === false) {
                die("Env var TEST_SERVER_URL is mandatory \n export TEST_SERVER_URL='/'");
            }
            $this->baseurl = $this->getEnvParam('TEST_SERVER_URL', $this->baseurl);
        }

        return $this->baseurl;
    }

    public function getUser()
    {
        if ($this->user === null) {
            $this->user = $this->getEnvParam('TEST_API_USER', $this->user);
        }

        return $this->user;
    }

    public function getPassword()
    {
        if ($this->password === null) {
            $this->password = $this->getEnvParam('TEST_API_PASSWORD', $this->password);
        }

        return $this->password;
    }

    public function getClientId()
    {
        if ($this->clientId === null) {
            $this->clientId = $this->getEnvParam('TEST_CLIENT_ID', $this->clientId);
        }

        return $this->clientId;
    }

    public function getClientSecret()
    {
        if ($this->clientSecret === null) {
            $this->clientSecret = $this->getEnvParam('TEST_CLIENT_SECRET', $this->clientSecret);
        }

        return $this->clientSecret;
    }

    public function login()
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendPOST(
            $this->getBaseUrl() . '/oauth/v2/token',
            [
                'client_id'     => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'grant_type'    => 'password',
                'username'      => $this->getUser(),
                'password'      => $this->getPassword(),
            ]
        );
        $this->token = $this->getOAuthToken();
        $this->refreshToken = $this->getOAuthRefreshToken();
    }

    private function getEnvParam(string $envParamName, $default = null): ?string
    {
        if (getenv($envParamName)) {
            return getenv($envParamName);
        }

        return $default;
    }

    /**
     * @param string|null $clientId
     */
    public function setClientId(?string $clientId = null): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string|null $clientSecret
     */
    public function setClientSecret(?string $clientSecret = null): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string|null $user
     */
    public function setUser(?string $user = null): void
    {
        $this->user = $user;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password = null): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
