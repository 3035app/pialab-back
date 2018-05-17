<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Entity\Oauth;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_client")
 */
class Client extends BaseClient
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
     * @ORM\Column(name="name", type="string", nullable=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     *
     * @var string
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="application")
     *
     * @var Collection
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="PiaApi\Entity\Oauth\AccessToken", mappedBy="client", cascade={"remove"})
     *
     * @var Collection
     */
    protected $tokens;

    public function __construct()
    {
        parent::__construct();

        $this->users = new ArrayCollection();
        $this->tokens = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    /**
     * @return Collection
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    /**
     * @param Collection $tokens
     */
    public function setTokens(Collection $tokens): void
    {
        $this->tokens = $tokens;
    }

    public function getNotExpiredTokens()
    {
        return $this->tokens->filter(function (AccessToken $item) {
            return $item->getExpiresAt() >= (new \DateTime())->getTimestamp();
        });
    }
}
