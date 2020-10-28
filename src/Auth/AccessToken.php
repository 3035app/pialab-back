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
use JMS\Serializer\Annotation as JMS;

class AccessToken
{
    /**
     * @Swg\Property(description="The access token")
     *
     * @JMS\Groups({"Default"})
     * @JMS\Type("string")
     *
     * @var string
     */
    private $access_token;

    /**
     * @Swg\Property(description="The expiry date of the generated token")
     *
     * @JMS\Groups({"Default"})
     * @JMS\Type("DateTime")
     *
     * @var string
     */
    private $expires_in;

    /**
     * @Swg\Property(description="The type of the token. Usually « bearer »")
     *
     * @JMS\Groups({"Default"})
     * @JMS\Type("string")
     *
     * @var string
     */
    private $token_type;

    /**
     * @Swg\Property(description="The OpenID scope (unused)")
     *
     * @JMS\Groups({"Default"})
     * @JMS\Type("string")
     *
     * @var string
     */
    private $scope;

    /**
     * @Swg\Property(description="The token that must be used to refresh the token")
     *
     * @JMS\Groups({"Default"})
     * @JMS\Type("string")
     *
     * @var string
     */
    private $refresh_token;
}
