<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 7/19/2018
 * Time: 5:39 PM
 */

namespace App\EventSubscriber;

use App\Exception\HttpJsonException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
            ]
        ];
    }

    public function processException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpJsonException) {
            $response = new JsonResponse($exception->getResponse(), $exception->getCode());
            $event->setResponse($response);
        }
    }

    public function logException(GetResponseForExceptionEvent $event)
    {
        // ...
    }

    public function notifyException(GetResponseForExceptionEvent $event)
    {
        // ...
    }
}