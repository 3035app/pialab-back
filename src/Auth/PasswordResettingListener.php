<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Auth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use PiaApi\Entity\Oauth\User;
use Symfony\Component\HttpFoundation\Response;

class PasswordResettingListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        );
    }

    public function onPasswordResettingSuccess(FormEvent $event)
    {
        /** @var User $user */
        $user = $event->getForm()->getData();

        $url = $user->getApplication()->getUrl();

        if ($url === null || $url === '') {
            // This case should never happen
            $event->setResponse(new Response('Password changed'));

            return;
        }

        $event->setResponse(new RedirectResponse($url));
    }
}
