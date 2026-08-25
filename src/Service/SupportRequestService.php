<?php

namespace App\Service;

use App\Entity\SupportRequest;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SupportRequestService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationInterface $notification,
        private LoggerInterface $logger,
    ) {
    }

    public function create(
        string $customerEmail,
        string $message,
    ): SupportRequest {
        $supportRequest = new SupportRequest();

        $supportRequest->setCustomerEmail($customerEmail);
        $supportRequest->setMessage($message);
        $supportRequest->setStatus('new');
        $supportRequest->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($supportRequest);
        $this->entityManager->flush();

        try {
            $this->notification->send(
                'New support request from ' . $customerEmail
            );
        } catch (NotificationException $e) {
            $this->logger->warning(
                'Support request notification failed',
                [
                    'customerEmail' => $customerEmail,
                    'exception' => $e,
                ]
            );
        }

        return $supportRequest;
    }
}
