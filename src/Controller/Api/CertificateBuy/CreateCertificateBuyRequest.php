<?php

namespace App\Controller\Api\CertificateBuy;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCertificateBuyRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public int $id,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public int $price,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public string $dateTimeAt,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public bool $after,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public string $message,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank(message: 'Значение не должно быть пустым')]
        public string $name,
    )
    {}
}