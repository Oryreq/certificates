<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\Api\ApiApplyCertificatesController;
use App\Controller\Api\ApiChangeEmailController;
use App\Controller\Api\ApiChangePasswordController;
use App\Controller\Api\ApiMeController;
use App\Controller\Api\ApiRegisterController;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['email'])]
#[Get(
    uriTemplate: '/me',
    controller: ApiMeController::class,
)]
#[Get(
    uriTemplate: '/apply-certificates',
    controller: ApiApplyCertificatesController::class,
)]
#[Post(
    uriTemplate: '/register',
    controller: ApiRegisterController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'username' => ['type' => 'string'],
                            'password' => ['type' => 'string'],
                            'email'    => ['type' => 'string'],
                        ]
                    ]
                ]
            ])
        )
    ),
    normalizationContext: ['groups' => 'user:item'],
)]
#[Post(
    uriTemplate: '/users/edit-email',
    controller: ApiChangeEmailController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'email'             => ['type' => 'string'],
                            'emailConfirmation' => ['type' => 'string'],
                        ]
                    ]
                ],
            ])
        ),
    ),
)]
#[Post(
    uriTemplate: '/users/edit-password',
    controller: ApiChangePasswordController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new \ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'password'             => ['type' => 'string'],
                            'passwordConfirmation' => ['type' => 'string'],
                        ]
                    ]
                ],
            ])
        ),
    ),
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLES = [
        'Администратор' => 'ROLE_ADMIN',
        'Пользователь' => 'ROLE_USER',
        'Тренерство' => 'ROLE_SUPER_ADMIN',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:item'])]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:item'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:item'])]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column]
    private ?int $coins = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getCoins(): ?int
    {
        return $this->coins;
    }

    public function setCoins(?int $coins): void
    {
        $this->coins = $coins;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
