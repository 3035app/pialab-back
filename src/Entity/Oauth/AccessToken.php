<?php

namespace PiaApi\Entity\Oauth;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;
use PiaApi\Entity\Traits\ResourceTrait;

/**
 * @ORM\Entity
 */
class AccessToken extends BaseAccessToken
{
    use ResourceTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Your\Own\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
}
