<?php

namespace App\Controller\Api\CertificateBuy;

use Symfony\Component\Validator\Constraints as Assert;

class PreviewCertificateBuyRequest
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