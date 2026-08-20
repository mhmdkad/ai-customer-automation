<?php

namespace App\Service;

interface NotificationInterface
{
    public function send(string $message): void;
}
