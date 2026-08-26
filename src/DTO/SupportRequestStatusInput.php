<?php

namespace App\DTO;

use App\Enum\SupportRequestStatus;
use Symfony\Component\Validator\Constraints as Assert;

class SupportRequestStatusInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [SupportRequestStatus::class, 'values'])]
        public string $status,
    ) {
    }
}
