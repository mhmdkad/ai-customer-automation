<?php

namespace App\Service;

class TestNotificationService implements NotificationInterface
{
    public function send(string $message): void
    {
        // Do nothing during tests.
    }
}
