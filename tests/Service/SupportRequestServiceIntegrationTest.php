<?php

namespace App\Tests\Service;

use App\Service\SupportRequestService;
use App\Entity\SupportRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SupportRequestServiceIntegrationTest extends KernelTestCase
{
    public function testCreatePersistsSupportRequest(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $service = $container->get(SupportRequestService::class);

        $supportRequest = $service->create(
            'integration@example.com',
            'Testing the real database',
        );

        $this->assertSame(
            'integration@example.com',
            $supportRequest->getCustomerEmail()
        );

        $this->assertSame(
            'Testing the real database',
            $supportRequest->getMessage()
        );

        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $savedRequest = $entityManager
            ->getRepository(SupportRequest::class)
            ->findOneBy([
                'customerEmail' => 'integration@example.com',
            ]);

        $this->assertNotNull($savedRequest);
    }
}
