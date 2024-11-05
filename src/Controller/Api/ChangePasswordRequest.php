<?php

namespace App\Controller\Api;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        #[Assert\Length(min: 8, minMessage: 'Пароль должен состоять минимум из 8 символов')]
        public string $password,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        #[Assert\Length(min: 8, minMessage: 'Пароль должен состоять минимум из 8 символов')]
        public string $passwordConfirmation,
    )
    {}
}