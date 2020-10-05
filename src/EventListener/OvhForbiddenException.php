<?php

namespace App\EventListener;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class OvhForbiddenException
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ClientException) {
            if ($exception->getCode() === 403) {
                $response = new RedirectResponse('/login/provider/ovh');
                $event->setResponse($response);
            }
        }
    }
}
