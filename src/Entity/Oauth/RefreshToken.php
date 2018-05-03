<?php

namespace PiaApi\Entity\Oauth;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_refresh_token")
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var string
     */
    protected $id;
    
    /**
         * @ORM\ManyToOne(targetEntity="PiaApi\Entity\Oauth\Client")
         * @ORM\JoinColumn(nullable=false)
         */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="PiaApi\Entity\Oauth\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
}
