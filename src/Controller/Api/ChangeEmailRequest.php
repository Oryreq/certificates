<?php

namespace App\Controller\Api;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmailRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        #[Assert\Email]
        public string $emailConfirmation,
    )
    {}
}