<?php

namespace App\Service;

class ApplicationInfo
{
    public function getName(): string
    {
        return 'AI Customer Automation';
    }

    public function getVersion(): string
    {
        return '0.1.0';
    }

    public function getEnvironment(): string
    {
        return 'development';
    }
}
