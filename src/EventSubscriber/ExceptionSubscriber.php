<?php
// src/EventSubscriber/ExceptionSubscriber.php

namespace App\EventSubscriber;

use App\Exception\TaskException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Log the exception
        $this->logger->error('Exception occurred: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
            'request_uri' => $request->getUri(),
            'method' => $request->getMethod(),
        ]);

        // Handle TaskException
        if ($exception instanceof TaskException) {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->getCode() ?: 500);

            $event->setResponse($response);
            return;
        }

        // Handle HttpException
        if ($exception instanceof HttpException) {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());

            $event->setResponse($response);
            return;
        }

        // Handle other exceptions
        $response = new JsonResponse([
            'status' => 'error',
            'message' => 'Internal server error',
        ], 500);

        $event->setResponse($response);
    }
}