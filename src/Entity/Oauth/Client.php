<?php

namespace PiaApi\Entity\Oauth;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use PiaApi\Entity\Traits\ResourceTrait;

/**
 * @ORM\Entity
 */
class Client extends BaseClient
{
    use ResourceTrait;

    public function __construct()
    {
        parent::__construct();
    }
}
