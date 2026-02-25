<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Логируем исключение с контекстом
        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
            'ip' => $request->getClientIp(),
        ]);

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Internal server error';

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            if ($exception instanceof NotFoundHttpException) {
                $message = 'Not found';
            } elseif ($exception instanceof AccessDeniedHttpException) {
                $message = 'Access denied';
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                $message = 'Method not allowed';
            } else {
                $message = Response::$statusTexts[$statusCode] ?? 'Error';
            }
        }

        $response = new JsonResponse([
            'status' => false,
            'message' => $message,
        ], $statusCode);

        $event->setResponse($response);
        $event->stopPropagation();
    }
}
