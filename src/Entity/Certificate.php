<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\Api\ApiCertificateBuysController;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\CertificateRepository;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model\Operation;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTime;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CertificateRepository::class)]
#[Vich\Uploadable]
#[Get]
#[GetCollection(
    paginationEnabled: false,
)]
#[Post(
    uriTemplate: '/certificate_buys/preview',
    controller: ApiCertificateBuysController::class,
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
class Certificate
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['certificate:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['certificate:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 1024)]
    #[Groups(['certificate:item'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['certificate:item'])]
    private ?string $shortDescription = null;

    #[ORM\Column]
    #[Groups(['certificate:item'])]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['certificate:item'])]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'certificates', fileNameProperty: 'image')]
    #[Assert\Image(mimeTypes: ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])]
    private ?File $imageFile = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

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
}
