<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiRegisterController extends AbstractController
{
    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public UserPasswordHasherInterface $passwordHasher;

    private array $USERNAME_ALREADY_EXIST_RESPONSE = [
        'status' => 'error',
        'details' => 'User with this username is already registered',
    ];

    private array $EMAIL_ALREADY_EXIST_RESPONSE = [
        'status' => 'error',
        'details' => 'User with this email is already registered',
    ];


    public function __invoke(#[MapRequestPayload] RegisterRequest $request): JsonResponse
    {
        if ($this->userRepository->existByUsername($request->username)) {
            return $this->json($this->USERNAME_ALREADY_EXIST_RESPONSE);
        }

        if ($this->userRepository->existByEmail($request->email)) {
            return $this->json($this->EMAIL_ALREADY_EXIST_RESPONSE);
        }

        $id = $this->saveUser($request);
        $response = [
            'id' => $id
        ];
        return $this->json($response);
    }

    private function saveUser(RegisterRequest $request): int
    {
        $user = (new User())
            ->setUsername($request->username)
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword($request->password)
            ->setEmail($request->email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->userRepository
                    ->findOneBy(['username' => $request->username])
                    ->getId();
    }
}