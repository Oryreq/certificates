<?php

namespace App\Controller\Api;

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
    )
    {}
}