<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 11:40
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Utils\Codes;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Codes::HTTP_NOT_FOUND));
            return;
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()]), Codes::HTTP_FORBIDDEN);
            return;
        }
    }
}