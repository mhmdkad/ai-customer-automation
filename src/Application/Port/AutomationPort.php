<?php

namespace App\Application\Port;

interface AutomationPort
{
    public function supportRequestCreated(
        int $supportRequestId,
        string $customerEmail,
        string $message,
    ): void;
}
