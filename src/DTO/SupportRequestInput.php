<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupportRequestInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $customerEmail,

        #[Assert\NotBlank]
        public string $message,
    ) {
    }
}
