<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ConsumerKeyNotFoundInSessionException;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ConsumerKeyNotFoundInSessionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if ($exception instanceof ConsumerKeyNotFoundInSessionException) {
            $response = new RedirectResponse('/login');

            $event->setResponse($response);
        }

        if ($exception instanceof ClientException) {
            if ($exception->getCode() === 403) {
                $response = new RedirectResponse('/login');

                $event->setResponse($response);
            }
        }
    }
}
