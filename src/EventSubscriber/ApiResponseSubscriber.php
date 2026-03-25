<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 255],
            KernelEvents::EXCEPTION => ['onKernelException', 255],
        ];
    }

    /**
     * Successful response
     */
    public function onKernelView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        // Check if controller returned JsonResponse - return it
        if ($result instanceof JsonResponse) {
            return;
        }

        $response = ['status' => true];

        // Wrap data
        if ($result !== null) {
            $response['data'] = $result;
        }

        $event->setResponse(new JsonResponse($response));
    }

    /**
     * Exception handler (validation errors, etc.)
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $responseData = [
            'status' => false,
            'data' => [
                'error' => $exception->getMessage()
            ]
        ];

        // Extract validation errors
        $previous = $exception->getPrevious();
        if ($previous instanceof ValidationFailedException) {
            $responseData['data']['error'] = 'Validation failed';
            $responseData['data']['errors'] = $this->formatValidationErrors($previous);
        }

        $event->setResponse(new JsonResponse($responseData, $statusCode));
        $event->stopPropagation();
    }

    private function formatValidationErrors(ValidationFailedException $exception): array
    {
        $errors = [];
        foreach ($exception->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $errors;
    }
}
