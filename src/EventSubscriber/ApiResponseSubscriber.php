<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationException;

class ApiResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW     => 'onKernelView',
            KernelEvents::EXCEPTION => 'onKernelException',
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
            $event->setResponse($result);
            return;
        }

        // Check if controller returned null — return success
        if ($result === null) {
            $response = new JsonResponse(['status' => true]);
            $event->setResponse($response);
            return;
        }

        // Wrapp array
        if (is_array($result)) {
            $response = new JsonResponse(['status' => true, 'data' => $result]);
            $event->setResponse($response);
            return;
        }

        // Wrap other data
        $response = new JsonResponse(['status' => true, 'data' => $result]);
        $event->setResponse($response);
    }

    /**
     * Exception handler (validation errors, abort(), etc.)
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = 500;
        $errors = null;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $errorMessage = $exception->getMessage() ?: 'An error occurred.';

            $previous = $exception->getPrevious();
            if ($previous instanceof ValidationException) {
                $statusCode = 400;
                $errorMessage = 'Validation failed.';
                $errors = $this->extractErrorsFromViolations($previous->getViolations());
            }
        } elseif ($exception instanceof ValidationException) {
            $statusCode = 400;
            $errorMessage = 'Validation failed.';
            $errors = $this->extractErrorsFromViolations($exception->getViolations());
        } else {
            $errorMessage = $exception->getMessage();
        }

        $responseData = [
            'status' => false,
            'data'   => ['error' => $errorMessage],
        ];

        if ($errors !== null) {
            $responseData['data']['errors'] = $errors;
        }

        $event->setResponse(new JsonResponse($responseData, $statusCode));
    }

    private function extractErrorsFromViolations($violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }
}
