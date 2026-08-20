<?php

namespace App\Service;

class EmailNotificationService implements NotificationInterface
{
    public function send(string $message): void
    {
        echo "Sending email: " . $message;
    }
}
