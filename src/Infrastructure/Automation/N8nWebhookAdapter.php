<?php

namespace App\Infrastructure\Automation;

use App\Application\Port\AutomationPort;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class N8nWebhookAdapter implements AutomationPort
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $webhookUrl,
    ) {
    }

    public function supportRequestCreated(
        int $supportRequestId,
        string $customerEmail,
        string $message,
    ): void {
        $this->httpClient->request(
            'POST',
            $this->webhookUrl,
            [
                'json' => [
                    'id' => $supportRequestId,
                    'customerEmail' => $customerEmail,
                    'message' => $message,
                ],
            ]
        );
    }
}
