<?php

namespace App\Tests\Service;

use App\Service\NotificationException;
use App\Service\NotificationInterface;
use App\Service\SupportRequestService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SupportRequestServiceTest extends TestCase
{
    public function testCreateCreatesSupportRequest(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager
            ->expects($this->once())
            ->method('persist');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $notification = $this->createMock(NotificationInterface::class);

        $notification
            ->expects($this->once())
            ->method('send')
            ->with('New support request from test@example.com');

        $logger = $this->createStub(LoggerInterface::class);

        $service = new SupportRequestService(
            $entityManager,
            $notification,
            $logger,
        );

        $supportRequest = $service->create(
            'test@example.com',
            'I cannot log in',
        );

        $this->assertSame(
            'test@example.com',
            $supportRequest->getCustomerEmail()
        );

        $this->assertSame(
            'I cannot log in',
            $supportRequest->getMessage()
        );

        $this->assertSame(
            'new',
            $supportRequest->getStatus()
        );
    }

    public function testCreateThrowsExceptionWhenNotificationFails(): void
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $notification = $this->createMock(NotificationInterface::class);

        $notification
            ->expects($this->once())
            ->method('send')
            ->willThrowException(new NotificationException('Notification failed'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning');

        $service = new SupportRequestService(
            $entityManager,
            $notification,
            $logger,
        );

        $service->create(
            'test@example.com',
            'I cannot log in',
        );
    }
}
