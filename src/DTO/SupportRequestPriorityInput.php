<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupportRequestPriorityInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['low', 'normal', 'high'])]
        public string $priority,
    ) {
    }
}
