<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\Api\CertificateBuy\ActivateCertificateBuyController;
use App\Controller\Api\CertificateBuy\CreateCertificateBuyController;
use App\Controller\Api\CertificateBuy\PreviewCertificateBuyController;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\CertificateBuyRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CertificateBuyRepository::class)]
#[Vich\Uploadable]
#[GetCollection(
    paginationEnabled: false,
    normalizationContext: ['groups' => ['certificateBuy:read']]
)]
#[Post(
    uriTemplate: '/certificate_buys/listing',
    controller: CreateCertificateBuyController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'id'         => ['type' => 'integer'],
                            'price'      => ['type' => 'integer'],
                            'dateTimeAt' => ['type' => 'string'],
                            'after'      => ['type' => 'boolean'],
                            'message'    => ['type' => 'string'],
                            'email'      => ['type' => 'string'],
                            'name'       => ['type' => 'string'],
                        ]
                    ]
                ]
            ])
        )
    ),
    normalizationContext: ['groups' => ['certificateBuy:read']],
)]
#[Post(
    uriTemplate: '/certificate_buys/preview',
    controller: PreviewCertificateBuyController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'id'         => ['type' => 'integer'],
                            'price'      => ['type' => 'integer'],
                            'dateTimeAt' => ['type' => 'string'],
                        ]
                    ]
                ]
            ])
        )
    )
)]
#[Get(
    uriTemplate: '/certificate_buys/checks',
    controller: ActivateCertificateBuyController::class,
    openapi: new Operation(
        parameters: [
            new Parameter(
                name: "code",
                in: "query",
                required: true,
                schema: [
                    'type' => 'string',
                ]
            )
        ]
    )
)]
class CertificateBuy
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('certificateBuy:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('certificateBuy:read')]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'certificate_images', fileNameProperty: 'image')]
    #[Assert\Image(mimeTypes: ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    #[Groups('certificateBuy:read')]
    private ?string $pdf = null;

    #[Vich\UploadableField(mapping: 'certificates', fileNameProperty: 'pdf')]
    private ?File $pdfFile = null;

    #[ORM\Column]
    #[Groups('certificateBuy:read')]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    #[Groups('certificateBuy:read')]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups('certificateBuy:read')]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups('certificateBuy:read')]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): static
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getPdfFile(): ?File
    {
        return $this->pdfFile;
    }

    public function setPdfFile(?File $pdfFile): self
    {
        $this->pdfFile = $pdfFile;
        if (null !== $pdfFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
