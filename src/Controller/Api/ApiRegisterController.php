<?php

namespace App\Controller\Api;

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


    public function __invoke(#[MapRequestPayload] RegisterRequest $request): JsonResponse
    {
        if ($this->userRepository->existByUsername($request->username)) {
            $response = [
                'status' => 'error',
                'details' => 'User with this username is already registered',
            ];
            return $this->json($response);
        }

        if ($this->userRepository->existByEmail($request->email)) {
            $response = [
                'status' => 'error',
                'details' => 'User with this email is already registered',
            ];
            return $this->json($response);
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