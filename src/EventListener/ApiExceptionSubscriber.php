<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use PiaApi\Exception\ApiException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if (!$e instanceof ApiException) {
            return;
        }

        $response = new JsonResponse(
            [
              'message'    => $e->getMessage(),
              'code'       => $e->getStatusCode(),
              'error_code' => $e->getCode(),
            ],
            $e->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/json');
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
              KernelEvents::EXCEPTION => 'onKernelException',
          );
    }
}
