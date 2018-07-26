<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Auth;

use Swagger\Annotations as Swg;

class AccessToken
{
    /**
     * @Swg\Property(description="The access token")
     *
     * @var string
     */
    private $access_token;

    /**
     * @Swg\Property(description="The expiry date of the generated token")
     *
     * @var string
     */
    private $expires_in;

    /**
     * @Swg\Property(description="The type of the token. Usually « bearer »")
     *
     * @var string
     */
    private $token_type;

    /**
     * @Swg\Property(description="The OpenID scope (unused)")
     *
     * @var string
     */
    private $scope;

    /**
     * @Swg\Property(description="The token that must be used to refresh the token")
     *
     * @var string
     */
    private $refresh_token;
}
