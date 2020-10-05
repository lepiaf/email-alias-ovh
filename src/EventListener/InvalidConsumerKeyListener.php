<?php

namespace App\EventListener;

use App\Exception\ConsumerKeyNotFoundInSessionException;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class InvalidConsumerKeyListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new RedirectResponse('/login/provider/ovh');
        if ($exception instanceof ClientException) {
            if ($exception->getCode() === 403) {
                $event->setResponse($response);
            }
        }

        if ($exception instanceof ConsumerKeyNotFoundInSessionException) {
            $event->setResponse($response);
        }
    }
}
