<?php

namespace App\Controller;

use App\Service\ApplicationInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController
{
    public function __construct(
    private ApplicationInfo $applicationInfo
) {
}
    #[Route('/api/health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
        ]);
    }

    #[Route('/api/status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return new JsonResponse([
            'application' => $this->applicationInfo->getName(),
            'version' => $this->applicationInfo->getVersion(),
            'environment' => $this->applicationInfo->getEnvironment(),
            'status' => 'running',
        ]);
    }
}
