<?php

namespace App\Service;

class SlackNotificationService implements NotificationInterface
{
    public function send(string $message): void
    {
        echo "Sending Slack message: " . $message;
    }
}
