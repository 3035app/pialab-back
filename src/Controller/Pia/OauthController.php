<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OauthController
{
    /**
     * @var OAuth2
     */
    protected $server;

    /**
     * @param OAuth2 $FOSOauthServer
     */
    public function __construct(OAuth2 $FOSOauthServer)
    {
        $this->server = $FOSOauthServer;
    }

    /**
     * Authenticate User to the API.
     *
     * @Swg\Tag(name="Auth")
     *
     * @Route("/oauth/v2/token", methods={"POST"})
     *
     * @Swg\Parameter(
     *     name="client_id",
     *     in="formData",
     *     type="string",
     *     description="The client ID corresponding to the Application created in backend"
     * )
     * @Swg\Parameter(
     *     name="client_secret",
     *     in="formData",
     *     type="string",
     *     description="The client secret corresponding to the Application created in backend"
     * )
     * @Swg\Parameter(
     *     name="grant_type",
     *     in="formData",
     *     type="string",
     *     description="The type of check that will be performed. Value is « password »."
     * )
     * @Swg\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="The username (or email) of an Application's User"
     * )
     * @Swg\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="The password of an Application's User"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the granted token object",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=PiaApi\Auth\AccessToken::class, groups={"Default"})
     *     )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
