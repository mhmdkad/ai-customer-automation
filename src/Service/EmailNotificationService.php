<?php

namespace App\Service;

class EmailNotificationService implements NotificationInterface
{
    public function send(string $message): void
    {
        error_log("Sending email: " . $message);
    }
}
