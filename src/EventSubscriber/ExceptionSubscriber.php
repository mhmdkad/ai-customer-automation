<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof HttpExceptionInterface) {
            return;
        }
        if (
            $event->getThrowable() instanceof AuthenticationException
            || $event->getThrowable() instanceof AccessDeniedException
        ) {
            return;
        }

        $this->logger->error(
            'Unhandled exception: ' . $event->getThrowable()::class,
            [
                'exception' => $event->getThrowable(),
            ]
        );

        $response = new JsonResponse([
            'error' => 'Internal server error',
        ], 500);

        $event->setResponse($response);
    }
}
