<?php

namespace App\Service;

use App\Entity\SupportRequest;
use Doctrine\ORM\EntityManagerInterface;

class SupportRequestService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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

        return $supportRequest;
    }
}
