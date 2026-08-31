<?php

namespace App\Tests\Controller;

use App\Entity\SupportRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SupportRequestControllerTest extends WebTestCase
{
    public function testUpdateStatusReturnsUpdatedSupportRequest(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $supportRequest = new SupportRequest();
        $supportRequest->setCustomerEmail('controller-test@example.com');
        $supportRequest->setMessage('Testing the PATCH endpoint');
        $supportRequest->setStatus('new');
        $supportRequest->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($supportRequest);
        $entityManager->flush();

        $client->request(
            'PATCH',
            '/api/support-requests/' . $supportRequest->getId(),
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer test-api-key',
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'status' => 'resolved',
            ]),
        );

        $this->assertResponseStatusCodeSame(200);

        $response = $client->getResponse();

        $data = json_decode(
            $response->getContent(),
            true,
        );

        $this->assertSame(
            'resolved',
            $data['status'],
        );
    }

    public function testUpdateStatusRejectsInvalidStatus(): void
    {
        $client = static::createClient();

        $client->request(
            'PATCH',
            '/api/support-requests/99999',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer test-api-key',
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'status' => 'banana',
            ]),
        );

        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateStatusReturnsNotFoundForMissingRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'PATCH',
            '/api/support-requests/99999',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer test-api-key',
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode([
                'status' => 'in_progress',
            ]),
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
