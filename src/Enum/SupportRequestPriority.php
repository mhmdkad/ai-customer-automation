<?php

namespace App\Enum;

enum SupportRequestPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
}
